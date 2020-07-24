<?php

class Recordings
{

    static public function all($sid, $token, $pageNum)
    {
        try {
            $client = new Twilio\Rest\Client($sid, $token);
            $pageSize = 1000;
            $result = array();
            $page = $client->api->v2010->recordings->page(array(), $pageSize, 1, $pageNum);
            foreach ($page as $key => $recording) {
                $result[] = array(
                    $recording->accountSid,
                    $token,
                    $recording->sid,
                    $recording->callSid,
                    $recording->dateCreated->format(DateTime::ATOM),
                    $recording->dateUpdated->format(DateTime::ATOM),
                    $recording->status,
                    $recording->source,
                    $recording->uri
                );
            }
            echo json_encode(array('header' => Recordings::header(), 'data' => $result, 'hasNextPage' => count($result) >= $pageSize));
        } catch (Exception $e) {
            echo json_encode(array('error' => 'Unable to fetch recordings. Error: (' . $e->getCode() . ') ' . $e->getMessage()));
        }
    }

    static public function header()
    {
        return array(
            "AccountSID",
            "AuthToken",
            "RecordingSID",
            "CallSID",
            "DateCreated",
            "DateUpdated",
            "Status",
            "Source",
            "URI"
        );
    }

    static public function delete($accountSid, $token, $recordingSid)
    {
        try {
            $client = new Twilio\Rest\Client($accountSid, $token);
            $recordingContext = $client->api->v2010->recordings($recordingSid);
            if ($recordingContext) {
                try {
                    $recordingContext->delete();
                    echo json_encode(array('success' => 'Recording with SID: ' . $recordingSid . ' deleted.'));
                } catch (Exception $e) {
                    echo json_encode(array('error' => 'Unable to delete recording with SID: ' . $recordingSid . '. Error: (' . $e->getCode() . ') ' . $e->getMessage()));
                }
            } else {
                echo json_encode(array('error' => 'Unable to find recording with SID: ' . $recordingSid . '. Already deleted?'));
            }
        } catch (Exception $e) {
            echo json_encode(array('error' => 'Unable to delete recording with SID: ' . $recordingSid . '. Error: (' . $e->getCode() . ') ' . $e->getMessage()));
        }
    }

}


