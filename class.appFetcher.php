<?php
/**
 * Description:
 * This class fetches ranking data from iTunes for a given appId for a given country and genre
 * 
 * About the author:
 * This script is written by Paul Peelen (http://www.PaulPeelen.com).
 * 
 * License:
 * You are allowed:
 *  - To use this script for personal and/or comercial use, free of charge
 *  - Distribute the script to third part.
 *  - Adapt/change the script to personal needs
 *  - Copy functions/parts of this script (but only with refference back to PaulPeelen.com)
 *  - Distribute the script with personal changes/additions, and include yourself into the functions comments credits for those functions you have changed/added.
 * You are NOT allowed:
 *  - To remove or chnage any credits or comments
 *  - To distribute the whole, or part off the script as your own (thus removing the credits).
 * 
 * You are using this script at your own risk. I (Paul Peelen) do not take any reponsibility for the way it used and/or functions.
 *
 * @author Paul Peelen <Paul@PaulPeelen.com>
 * @since v0.2 - 8 mar 2011
 * @todo Add comments for all the functions
 * @todo Create documentation
 */
 
 class appFetcher {
 	private $iAppId;
 	private $sCountryCode = "us";
 	private $bPaidApp = true;
 	private $iTopRange = 100;
 	private $sUrl;
 	private $aGenreArray = array();
 	private $iGenre = 0;
 	private $sData;
 	private $aRoughResult = array();
 	private $aMetaData = array();
 	private $aApps = array();
 	private $aAppData = array();
 	private $iAppPosition = 0;
 	
 	public function appFetcher ($a_iAppId, $a_sCountryCode, $a_bIsPaidApp, $a_iTopRange, $iGenre = 0)
 	{
 		$this->iAppId = $a_iAppId;
 		$this->sCountryCode = $a_sCountryCode;
 		$this->bPaidApp = $a_bIsPaidApp;
 		$this->iTopRange = $a_iTopRange;
 		$this->iGenre = $iGenre;
 		
 		$this->aGenreArray[0]		= "All Genres";
 		$this->aGenreArray[6018]	= "Books";
		$this->aGenreArray[6000]	= "Business";
		$this->aGenreArray[6017]	= "Education";
		$this->aGenreArray[6016]	= "Entertainment";
		$this->aGenreArray[6015]	= "Finance";
		$this->aGenreArray[6014]	= "Games";
		$this->aGenreArray[6013]	= "Healthcare &amp; Fitness";
		$this->aGenreArray[6012]	= "Lifestyle";
		$this->aGenreArray[6020]	= "Medical";
		$this->aGenreArray[6011]	= "Music";
		$this->aGenreArray[6010]	= "Navigation";
		$this->aGenreArray[6009]	= "News";
		$this->aGenreArray[6008]	= "Photography";
		$this->aGenreArray[6007]	= "Productivity";
		$this->aGenreArray[6006]	= "Reference";
		$this->aGenreArray[6005]	= "Social Networking";
		$this->aGenreArray[6004]	= "Sports";
		$this->aGenreArray[6003]	= "Travel";
		$this->aGenreArray[6002]	= "Utilities";
		$this->aGenreArray[6001]	= "Weather";
 		
 		$this->createUrlForRequest();
 		$this->fetchDataFromiTunes();
 		$this->translateData();
 	}
 	
 	public function getAppPosition ()
 	{
 		return $this->iAppPosition;
 	}
 	
 	public function getAppData ()
 	{
 		if (count($this->aAppData) == 0)
 		{
 			return false;
 		}
 		else {
 			return $this->aAppData;
 		}	
 	}

 	public function getCountryCode ()
 	{
 		return strtolower($this->sCountryCode);
 	}
 	
 	public function getCategoryName ()
 	{
 		return $this->aGenreArray[$this->iGenre];
 	}
 	
 	public function getAppId ()
 	{
 		return $this->iAppId;
 	}
 	
 	/**
 	 * Getting the icon from the HTML
 	 * 
 	 * @author paulp
 	 * @since 0.2 - 11 mar 2011
 	 */
 	private function getAppIconFromHtml ($sHtml)
 	{
 		preg_match_all('/<img[^>]+>/i', $sHtml, $aImages);
 		
 		$sUrl	= "";
 		
 		foreach ($aImages[0] as $sImage)
 		{
 			if (strpos($sImage, ' artwork'))
 			{
 				preg_match("/\< *[img][^\>]*[src] *= *[\"\']{0,1}([^\"\'\ >]*)/i", $sImage, $aResult);
 				
 				$sUrl	= $aResult[1];
 			}
 		}
 		
 		return $sUrl; 
 	}
 	
 	private function fetchDataFromiTunes ()
 	{
 		$oCurl	= curl_init($this->sUrl);
		curl_setopt ($oCurl, CURLOPT_RETURNTRANSFER, 1) ;
		curl_setopt($oCurl, CURLOPT_HEADER, 0);
		$this->sData = curl_exec($oCurl);
		curl_close($oCurl);
 	}
 	
 	private function translateData ()
 	{
 		$oXml = @simplexml_load_string($this->sData);
		$this->aRoughResult = $this->xmlToArray($oXml);
		
		$aAppList	= array();
		
		foreach ((is_array($this->aRoughResult['entry']) ? $this->aRoughResult['entry'] : array()) as $iNumber => $aData)
		{
			$iPosition	= $iNumber+1;
			
			$iAppId	= $this->extractAppIdFromUrl($aData['id']);
			
			$aAppList[$iAppId] = $aData;
			$aAppList[$iAppId]['position'] = $iPosition;
			
			$aTitleAuthor	= $this->separateTitleAndAuthor($aData['title']);
			$aAppList[$iAppId]['author'] = $aTitleAuthor['author'];
			$aAppList[$iAppId]['title'] = $aTitleAuthor['title'];
			$aAppList[$iAppId]['icon']	= $this->getAppIconFromHtml($aData['content']);
			
			if ($iAppId == $this->iAppId)
			{
				$this->aAppData	= $aAppList[$iAppId];
				$this->iAppPosition = $iPosition;
			}
		}
		
		$this->aApps	= $aAppList;
 	}
 	
 	private function extractAppIdFromUrl ($sUrl)
 	{
 		return (int)str_replace('id','',basename(parse_url($sUrl, PHP_URL_PATH)));
 	}
 	
 	private function separateTitleAndAuthor ($sTitle)
 	{
 		$aExplode	= explode(" - ", $sTitle);
 		
 		$sAuthor	= $aExplode[count($aExplode)-1];
 		$iPos		= strpos($sTitle, $sAuthor);
 		$sTitle		= substr($sTitle, 0, $iPos-3);
 		
 		return array("author" => $sAuthor, "title" => $sTitle);
 	}
 	
 	private function xmlToArray($obj, $level=0)
 	{ 	
	 	$aResult = array();
	 	
	 	if(!is_object($obj)) return $aResult;
	 	
	 	$aChild = (array)$obj;
	 	
	 	if(sizeof($aChild)>1)
	 	{
		 	foreach($aChild as $sName => $mValue)
		 	{
			 	if(is_array($mValue))
			 	{
				 	foreach($mValue as $ee=>$ff)
				 	{
					 	if(!is_object($ff))
					 	{
						 	$aResult[$sName][$ee] = $ff;
					 	} else if(get_class($ff)=='SimpleXMLElement')
					 	{
						 	$aResult[$sName][$ee] = $this->xmlToArray($ff,$level+1);
						}
				 	}
			 	} else if(!is_object($mValue))
			 	{
			 		$aResult[$sName] = $mValue;
				} else if(get_class($mValue)=='SimpleXMLElement')
				{
					$aResult[$sName] = $this->xmlToArray($mValue,$level+1);
				}
			}
	 	} else if(sizeof($aChild)>0)
	 	{
	 		foreach($aChild as $sName=>$mValue)
	 		{
	 			if(!is_array($mValue)&&!is_object($mValue))
	 			{
	 				$aResult[$sName] = $mValue;
				} else if(is_object($mValue))
				{
					$aResult[$sName] = $this->xmlToArray($mValue,$level+1);
				} else
				{
					foreach($mValue as $sNameTwo => $sValueTwo)
					{
						if(!is_object($sValueTwo))
						{
							$aResult[$obj->getName()][$sNameTwo] = $sValueTwo;
						} else if(get_class($sValueTwo)=='SimpleXMLElement')
						{
							$aResult[$obj->getName()][$sNameTwo] = $this->xmlToArray($sValueTwo,$level+1);
						}
					}
				}
			}
		}
		 	
	 	return $aResult;
 	}
 	
 	private function createUrlForRequest()
 	{
 		if (!$this->validateCountryCode())
 		{
 			$this->presentErrorMessage("Invalid country code selected. Please select different country code.", 1);
 			return false;
 		}
 		else if (!$this->validateTopRange())
 		{
 			$this->presentErrorMessage("Invalid range selected. The range should be between 1 and 300.", 2);
 			return false;
 		}
 		else {
 			$this->sUrl	= "http://itunes.apple.com/".$this->sCountryCode."/rss/".($this->bPaidApp ? "toppaidapplications" : "topfreeapplications")."/limit=".$this->iTopRange . ($this->iGenre > 0 ? "/genre=" . $this->iGenre : "") . "/xml";
 		}
 	}
 	
 	private function validateTopRange ()
 	{
 		$this->iTopRange = (int)$this->iTopRange;
 		
 		if ($this->iTopRange < 1 || $this->iTopRange > 300)
 		{
 			return false;
 		}
 		else {
 			return true;
 		}
 	}
 	
 	private function validateCountryCode ()
 	{
 		switch ( strtoupper($this->sCountryCode) ) {
			case "AR":
			case "AU":
			case "AT":
			case "BE":
			case "BR":
			case "CA":
			case "CL":
			case "CN":
			case "CO":
			case "CR":
			case "HR":
			case "CZ":
			case "DK":
			case "SV":
			case "FI":
			case "FR":
			case "DE":
			case "GR":
			case "GT":
			case "HK":
			case "HU":
			case "IN":
			case "ID":
			case "IE":
			case "IL":
			case "IT":
			case "JP":
			case "KR":
			case "KW":
			case "LB":
			case "LU":
			case "MY":
			case "MX":
			case "NL":
			case "NZ":
			case "NO":
			case "PK":
			case "PA":
			case "PE":
			case "PH":
			case "PL":
			case "PT":
			case "QA":
			case "RO":
			case "RU":
			case "SA":
			case "SG":
			case "SK":
			case "SI":
			case "ZA":
			case "ES":
			case "LK":
			case "SE":
			case "CH":
			case "TW":
			case "TH":
			case "TR":
			case "GB":
			case "US":
			case "AE":
			case "VE":
			case "VN":
				$this->sCountryCode = strtoupper($this->sCountryCode);
				return true;
				break;
				
			default:
				return false;
				break;
		}
 	}
 	
 	private function presentErrorMessage ($sMessage, $iErrorId)
 	{
 		echo "<b>appFetcher error!</b><br/>";
 		echo "<b>Error ID:</b> " . $iErrorId . "<br/>";
 		echo $sMessage;
 		exit;
 	}
 }
?>
