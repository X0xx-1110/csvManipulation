<?php


/**
 * cash-widthrow.php
 *
 * 
 *
 * @category   cash
 * @package    cash
 * @author     Kaloyan Hristov
 * @copyright  2021 Kaloyan Hristov
 * @license    -
 * @version    [0.0.1]
 * @link       -
 * @see        -
 * @since      File available since Release 0.0.1
 * @deprecated N/A
 */

namespace Kalata\Tricks;

class CashWithdraw{
	public string $cash;
	public array $filedata;
	public array $fee;
	public function _fileRead(string $file) : string|bool
    {
        if (isset($file) && ! empty($file) && is_file($file) && filesize($file) > 0) {
            $action = fopen($file, "r");
            $read = fread($action, filesize($file));
            fclose($action);
            if (isset($read) && ! empty($read) && strlen($read) > 1) {
                return $read;
            }
        }
        return false;
    }
    /**
    * create file read posibilities.
    * @return bool
    **/
    public function _fileWrite(string $filename, string $message) : bool
    {
        if(isset($filename) && ! empty($filename) && is_string($filename) && strlen($filename) > 4 && isset($message) && ! empty($message) && is_string($message) && strlen($message) > 1) {
            touch($filename);
            $handling = fopen($filename, "w+");
            fwrite($handling, $message."\n");
            fclose($handling);
            return true;
        }
        return false;
    }
    /**
    * All time generate CSV file again.
    * @return bool
    **/
    public function generateCSV(){
    	$dataForWrite = "";
    	$date = array();
    	$number = array();
    	$types = array(1=>"private", 2=>"business");
    	$operations = array(1=>"deposit", 2=>"withdraw");
    	$amount = array();
    	$currency = array(1=>"EUR",2=>"USD",3=>"JPY");

    	for($i=0; $i<100; $i++) {
    		$date[$i]=date("Y-m-d");
    		$number[$i]=rand(1,4);
    		$type[$i]=$types[rand(1,2)];
    		$operation[$i]=$operations[rand(1,2)];
    		$amount[$i]=(float) rand(0,1000).".".rand(1,9).rand(1,9); 
    		$currency[$i]=$currency[rand(1,3)];
    		$dataForWrite.=$date[$i].",".$number[$i].",".$type[$i].",".$operation[$i].",".$amount[$i].",".$currency[$i]."\n";
    	}
    	$this->_fileWrite("input.csv", $dataForWrite);
    	return TRUE;
    }
    /**
    * Calculation  of a generate data!.
    * @return bool
    **/
    public function calculation(array $payeeData)
    {
    	$this->fee = array();
    	$arrayCount = array();
    	if(isset($payeeData) && !empty($payeeData) && is_array($payeeData) && count($payeeData) ) {
    		foreach($payeeData as $key => $value) {
		    	if(isset($value[3]) && $value[3] !== "withdraw") {
		    		$this->fee[$key]= (float) "0.00";
		    		continue;
		    	}

		    	if( isset($value[1]) && !isset($arrayCount[$value[1]]) ) {
		    		$arrayCount[$value[1]]=1;
		    	}
		    	elseif( isset($value[1])) {
		    		$arrayCount[$value[1]]=$arrayCount[$value[1]]+1;
		    	}

		    	

		    	if(isset($value[1]) && isset($value[2]) && $value[2] == "private") {
			    	if( isset($arrayCount[$value[1]])) {
			    		if($arrayCount[$value[1]] > 3) {
			    			if(($value[4] * 0.003) > 0.03) {
			    				$this->fee[$key] = $value[4] * 0.003;
			    			}
			    			else {
			    				$this->fee[$key] = 0.03;
			    			}
			    		}
			    		else {
			    			$this->fee[$key] = 0.00;
			    		}
			    	}
			    	else {

			    	}
		    	}
		    	elseif(isset($value[2]) && $value[2] == "business") {
		    		if(($value[4] * 0.003) > 0.03) {
			    		$this->fee[$key] = $value[4] * 0.005;
			    	}
			    	else {
			    		$this->fee[$key] = 0.03;
			    	}
		    	}
	    	}
    	}
    	return TRUE;
    }
    /**
    * Prepare after read data for manuipulation!
    * @return array
    **/
    public function prepare($arg) {
    	if(isset($arg) && !empty($arg)) {
			$this->cash = $arg;
		}
		else {
			exit();
		}
		$tmpFiledata = $this -> _fileRead(addslashes($this -> cash));
		$tmpFiledata = explode("\n",$tmpFiledata);
		if(isset($tmpFiledata) && !empty($tmpFiledata)) {
			foreach($tmpFiledata as $key=>$value) {
				$tmpFiledataSecond[$key] = explode(",",$value);
			}
		}
		return $tmpFiledataSecond;
	}
}


// started as #php cash-widthrow.php input.csv
$cash = new CashWithdraw();
$cash->generateCSV();
$tmp = $cash->prepare($argv[1]);
$cash->calculation($tmp);
echo "<pre>";
var_dump( $cash->fee );



?>