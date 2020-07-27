<?php

class SubAccounts
{

    static public function all($sid, $token, $pageNum, $pageToken)
    {
        try {
            $client = new Twilio\Rest\Client($sid, $token);
            $pageSize = 30;
            $result = array();
            $page = $client->api->v2010->accounts->page(array(), $pageSize, $pageToken, $pageNum);
            foreach ($page as $key => $account) {
                if ($sid !== $account->sid) {
                    $result[] = array(
                        $account->sid,
                        $account->authToken,
                        $account->friendlyName,
                        $account->status
                    );
                }
            }

            if ($page->getNextPageUrl() != null) {
                $parts = parse_url($page->getNextPageUrl());
                parse_str($parts['query'], $queryParams);
                $pageToken = $queryParams['PageToken'];
            }

            echo json_encode(array('header' => SubAccounts::header(), 'data' => $result, 'hasNextPage' => $page->getNextPageUrl() != null, 'pageToken' => !is_null($pageToken) ? $pageToken : 1));
        } catch (Exception $e) {
            echo json_encode(array('error' => 'Unable to fetch phone numbers. Error: (' . $e->getCode() . ') ' . $e->getMessage()));
        }
    }

    static public function header()
    {
        return array(
            "SubAccountSID",
            "AuthToken",
            "FriendlyName",
            "Status"
        );
    }

    static public function status($accountSid, $token, $subAccountSid, $status)
    {
        if ($status !== 'active' && $status !== 'suspended') {
            echo json_encode(array('error' => 'Unable to change sub-account status with SID: ' . $subAccountSid . '. Not allowed status value: ' . $status));
            return;
        }
        try {
            $client = new Twilio\Rest\Client($accountSid, $token);
            $accountContext = $client->api->v2010->accounts($subAccountSid);
            if ($accountContext) {
                try {
                    $accountContext->update(array("status" => $status));
                    echo json_encode(array('success' => 'Changed sub-account status with SID: ' . $subAccountSid . ' to ' . $status . '.'));
                } catch (Exception $e) {
                    echo json_encode(array('error' => 'Unable to change sub-account status with SID: ' . $subAccountSid . '. Error: (' . $e->getCode() . ') ' . $e->getMessage()));
                }
            } else {
                echo json_encode(array('error' => 'Unable to change sub-account status with SID: ' . $subAccountSid . '.'));
            }
        } catch (Exception $e) {
            echo json_encode(array('error' => 'Unable to change sub-account status with SID: ' . $subAccountSid . '. Error: (' . $e->getCode() . ') ' . $e->getMessage()));
        }
    }

}


