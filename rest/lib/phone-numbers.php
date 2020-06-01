<?php

class PhoneNumbers
{

    static public function all($sid, $token, $pageNum)
    {
        try {
            $client = new Twilio\Rest\Client($sid, $token);
            $pageSize = 500;
            $result = array();
            $page = $client->incomingPhoneNumbers->page(array(), $pageSize, null, $pageNum);
            foreach ($page as $key => $phoneNumber) {
                $result[] = array(
                    $phoneNumber->accountSid,
                    $token,
                    $phoneNumber->sid,
                    $phoneNumber->phoneNumber,
                    $phoneNumber->dateCreated->format(DateTime::ATOM),
                    $phoneNumber->dateUpdated->format(DateTime::ATOM),
                    $phoneNumber->friendlyName,
                    $phoneNumber->voiceUrl,
                    $phoneNumber->voiceFallbackUrl
                );
            }
            echo json_encode(array('header' => PhoneNumbers::header(), 'data' => $result, 'hasNextPage' => count($result) >= $pageSize));
        } catch (Exception $e) {
            echo json_encode(array('error' => 'Unable to fetch phone numbers. Error: (' . $e->getCode() . ') ' . $e->getMessage()));
        }
    }

    static public function header()
    {
        return array(
            "AccountSID",
            "AuthToken",
            "NumberSID",
            "PhoneNumber",
            "DateCreated",
            "DateUpdated",
            "FriendlyName",
            "VoiceURL",
            "VoiceFallbackURL"
        );
    }

    static public function delete($accountSid, $token, $phoneNumSid)
    {
        try {
            $client = new Twilio\Rest\Client($accountSid, $token);
            $phoneNumContext = $client->incomingPhoneNumbers($phoneNumSid);
            if ($phoneNumContext) {
                try {
                    $phoneNumContext->delete();
                    echo json_encode(array('success' => 'Phone number with SID: ' . $phoneNumSid . ' deleted.'));
                } catch (Exception $e) {
                    echo json_encode(array('error' => 'Unable to delete phone number with SID: ' . $phoneNumSid . '. Error: (' . $e->getCode() . ') ' . $e->getMessage()));
                }
            } else {
                echo json_encode(array('error' => 'Unable to find phone number with SID: ' . $phoneNumSid . '. Already deleted?'));
            }
        } catch (Exception $e) {
            echo json_encode(array('error' => 'Unable to delete phone number with SID: ' . $phoneNumSid . '. Error: (' . $e->getCode() . ') ' . $e->getMessage()));
        }
    }

}


