<?php

function formatcurrency($anumber)
{
	$strnumber = $anumber . "";
	$strnumber = explode(".", $strnumber);
	$decnumber = str_split(($strnumber[0] . ""));
	$newdecnumber = "";

	$countdown = count($decnumber);
	$j = 1;
	for ($i = 0; $i < count($decnumber); $i++) {

		$newdecnumber = $decnumber[($countdown - 1)] . "" . $newdecnumber;
		$countdown--;
		$j++;
		if ($j > 3) {
			if ($i < (count($decnumber) - 1)) {
				$newdecnumber =  "," . $newdecnumber;
			}
			$j = 1;
		}
	}
	if (count($strnumber) > 1) {
		$newdecnumber = $newdecnumber . "." . $strnumber[1];
	}
	return $newdecnumber;
}


    /**
     * send sms
     *
     * @param string $recipients - comma separated string
     * @param string $messg - sms message
     * @param string $sender - sender
     * @param string $user - logged in user
     * @param string $customer_sms - YES | NO | EXCEMPT
     * @param boolean $groupsms - true if bulk sms, false for single
     * @return boolean
     */
    function sendSMS($recipients, $messg, $user, $customer_sms,$status, $groupsms = false)
    {
        //check internet
        $internet_connection = @fsockopen("www.google.com", 80);
        if (!$internet_connection) {
            return false;
        }

        //$dbmodel = new BaseModel();
        //$companydata = $dbmodel->findAll("organisationinfo")[0];
        $companyname = "How To Grow"; //$companydata["companyname"];
        $smssnder = "OROKAM-MF"; //$companydata["smssenderid"];

        $recipients = str_replace(",0", ",234", $recipients);
        $recipients = substr_replace($recipients, "234", 0, 1);
        $recipients = explode(",", $recipients);
        $sender = substr($smssnder, 0, 10);

        // log to find out if sms can be sent
        /*$resp = self::logApiUsageCount($companyname, "SMS USAGE", COMPANY_ID, $user);

        if (!$resp['status']) {
            if ($groupsms) {
                exit("<li class='list-group-item px-1 py-0 m-0 bg-danger text-white'><small>Service Disabled, contact support</small></li>");
            }
        }*/

        $payload = [];
        $payload["account"] = [
            "password" => "XMUB1Coy",
            "systemId" => "NG.105.0620"
        ];
        foreach ($recipients as $tel) {
            $payload["destinations"][] = $tel;
        }
        $payload["src"] = $sender;
        $payload["text"] = $messg;

        $payload = json_encode($payload);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://sms.vanso.com/rest/sms/submit/bulk",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json"
            ),
        ));

        $result = curl_exec($curl);
        $err = curl_error($curl);
        //$verificationcode = mt_rand(6,111111111);
        
        if (isset($result['errorCode'])) {
            // errorhandlerSMS($result . "; $sender");
            return false;
        } else {
            if (!$groupsms) {
                $dbmodel = new Model();
                $tlog = $_SESSION["userid"] . " " . date("D, d M Y  H:m:s");
                $date = date("Y-m-d");
                $accountnumber = trim($user);
                $fields = "date,accountnumber,status,verificationcode,tlog";
                $values = "'" . date('Y-m-d H:i:s') . "','" . $accountnumber . "','" . $status . "','" . $messg . "', '" . $tlog . "'";
                if ($customer_sms === "YES") {
                    //$dbmodel->create("smslogs", "`date`, `accountnumber`,`tlog`", "'$date', '$accountnumber','$tlog'");
                    $dbmodel->insertdata('smslogs', $fields, $values);
                }
            }
            return true;
        }
    }