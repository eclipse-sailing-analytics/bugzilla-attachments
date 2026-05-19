<?php
/**
* @todo
* process: check the last update timestamps from the RMS file. If newer than $lastRunTime, download and process
* keep $lastRuntime for all of the Authorities GER, ARG etc, so that failed downloads are not updated. Try again next day, issue warning when failed more than x times
* drop any elements from the array when lastUpdate of the certificate is older than lastRuntime for that  authority. process the new ones into the DB
* @todo
* sailnumber split - authority is often a sportsboat etc. try right to left? any letter to the right is suffix, then any number until either space or letter is hit, then the rest into authority.
* Check max length of authority etc. Suffix shouldn't exceed x characters either for the db
* @todo
* error corrections on the imported data - integrity check etc. What happens when e.g. (float) xyc runs?
* 
*/


// obtain a list of certificate countries and files
libxml_use_internal_errors(true);
$sUrl = 'http://data.orc.org/public/WPub.dll';
$sDownloadUrl = $sUrl.'?action=DownRMS&CountryId=%s';
$xmlAvailableRms = simplexml_load_file($sUrl);
$aXmlErrors = libxml_get_errors();
if (!empty($aXmlErrors)):
    var_dump($aXmlErrors);
endif;
foreach ($xmlAvailableRms->DATA->ROW as $xmlRmsFile) :
    $sDownload = sprintf($sDownloadUrl, $xmlRmsFile->CountryId);
    echo $xmlRmsFile->CountryId;
    echo $xmlRmsFile->CountryName;
    echo $xmlRmsFile->LastUpdate;
    echo $xmlRmsFile->CertCount;
    echo $xmlRmsFile->IntlCert;
    echo $xmlRmsFile->ClubCert;
endforeach;

/*
$s = 'OTNLOW  OTNMED  OTNHIG  ITNLOW  ITNMED  ITNHIG DH_TOD DH_TOT  PLT-I PLD-I TMF-OF  PLT2H PLD2H    OSN ReferenceNo    CDL    DSPS     WSS    MAIN   GENOA     SYM    ASYM  SY_FL SY_FMo  SY_FM  SY_FH  SY_RL SY_RMo  SY_RM  SY_RH  TN_FL TN_FMo  TN_FM  TN_FH  TN_RL TN_RMo  TN_RM  TN_RH';
preg_match_all('#\s(\w+)#', $s, $matches);
foreach ($matches[1] as $f) {
    printf("'$f' => array('length' => 7, 'type' => 'A'),\r\n");
    
}
die();
*/
$fp = fopen('GER2015.rms','r') or die ("can't open file");
$fp = fopen('GER2015.rms','r') or die ("can't open file");

$aRmsConfiguration = array(
                    'Nationality' => array('length' => 3, 'type' => 'A', 'conversion' => null),
                    'CertificateNumber' => array('length' => 6, 'type' => 'A', 'conversion' => 'int'),
                    'FileId' => array('length' => 8, 'type' => 'A', 'conversion' => null),
                    'SailNumber' => array('length' => 12, 'type' => 'A', 'conversion' => 'SailNumber'),
                    'BoatName' => array('length' => 24, 'type' => 'A', 'conversion' => null),
                    'DesignType' => array('length' => 18, 'type' => 'A', 'conversion' => null),
                    'Builder' => array('length' => 18, 'type' => 'A', 'conversion' => null),
                    'Designer' => array('length' => 18, 'type' => 'A', 'conversion' => null),
                    'Year' => array('length' => 5, 'type' => 'A', 'conversion' => 'int'),
                    'Club' => array('length' => 36, 'type' => 'A', 'conversion' => null),
                    'Owner' => array('length' => 36, 'type' => 'A', 'conversion' => null),
                    'Address1' => array('length' => 36, 'type' => 'A', 'conversion' => null),
                    'Address2' => array('length' => 36, 'type' => 'A', 'conversion' => null),
                    'CertificateType' => array('length' => 9, 'type' => 'A', 'conversion' => 'CertificateType'),
                    'D' => array('length' => 2, 'type' => 'A', 'conversion' => null),
                    'CrewWeight' => array('length' => 5, 'type' => 'A', 'conversion' => 'int'),
                    'CertificateDate' => array('length' => 20, 'type' => 'A', 'conversion' => 'datetime'),
                    'LOA' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'IMSL' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'Draft' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'Bmax' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'Displacement' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'Index' => array('length' => 8, 'type' => 'A', 'conversion' => 'float'),
                    'DA' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'GPH' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'TMF' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'ILCGA' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'PLT-O' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'PLD-O' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'WL6' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'WL8' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'WL10' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'WL12' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'WL14' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'WL16' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'WL20' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OL6' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OL8' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OL10' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OL12' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OL14' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OL16' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OL20' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'CR6' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'CR8' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'CR10' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'CR12' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'CR14' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'CR16' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'CR20' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'NSP6' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'NSP8' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'NSP10' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'NSP12' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'NSP14' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'NSP16' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'NSP20' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OC6' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OC8' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OC10' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OC12' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OC14' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OC16' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OC20' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'UA6' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'UA8' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'UA10' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'UA12' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'UA14' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'UA16' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'UA20' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'DA6' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'DA8' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'DA10' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'DA12' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'DA14' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'DA16' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'DA20' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'UP6' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'UP8' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'UP10' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'UP12' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'UP14' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'UP16' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'UP20' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R526' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R528' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R5210' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R5212' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R5214' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R5216' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R5220' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R606' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R608' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R6010' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R6012' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R6014' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R6016' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R6020' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R756' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R758' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R7510' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R7512' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R7514' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R7516' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R7520' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R906' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R908' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R9010' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R9012' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R9014' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R9016' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R9020' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R1106' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R1108' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R11010' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R11012' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R11014' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R11016' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R11020' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R1206' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R1208' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R12010' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R12012' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R12014' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R12016' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R12020' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R1356' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R1358' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R13510' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R13512' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R13514' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R13516' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R13520' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R1506' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R1508' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R15010' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R15012' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R15014' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R15016' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'R15020' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'D6' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'D8' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'D10' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'D12' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'D14' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'D16' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'D20' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OTNLOW' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OTNMED' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'OTNHIG' => array('length' => 8, 'type' => 'A', 'conversion' => 'float'),
                    'ITNLOW' => array('length' => 8, 'type' => 'A', 'conversion' => 'float'),
                    'ITNMED' => array('length' => 8, 'type' => 'A', 'conversion' => 'float'),
                    'ITNHIG' => array('length' => 8, 'type' => 'A', 'conversion' => 'float'),
                    'DH_TOD' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'DH_TOT' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'PLT' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'PLD' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'TMF-OF' => array('length' => 8, 'type' => 'A', 'conversion' => 'float'),
                    'PLT2H' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'PLD2H' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'OSN' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'ReferenceNo' => array('length' => 13, 'type' => 'A', 'conversion' => null),
                    'CDL' => array('length' => 6, 'type' => 'A', 'conversion' => 'float'),
                    'DSPS' => array('length' => 8, 'type' => 'A', 'conversion' => 'int'),
                    'WSS' => array('length' => 8, 'type' => 'A', 'conversion' => 'float'),
                    'MAIN' => array('length' => 8, 'type' => 'A', 'conversion' => 'float'),
                    'GENOA' => array('length' => 8, 'type' => 'A', 'conversion' => 'float'),
                    'SYM' => array('length' => 8, 'type' => 'A', 'conversion' => 'float'),
                    'ASYM' => array('length' => 8, 'type' => 'A', 'conversion' => 'float'),
                    'SY_FL' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'SY_FMo' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'SY_FM' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'SY_FH' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'SY_RL' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'SY_RMo' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'SY_RM' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'SY_RH' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'TN_FL' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'TN_FMo' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'TN_FM' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'TN_FH' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'TN_RL' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'TN_RMo' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'TN_RM' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    'TN_RH' => array('length' => 7, 'type' => 'A', 'conversion' => 'float'),
                    );
$sRmsConfiguration = '';
foreach($aRmsConfiguration as $field => $aFieldConfig) :
    $sRmsConfiguration .= $aFieldConfig['type'].(string)$aFieldConfig['length'].$field.'/';
endforeach;
$result = array();
$i = 0;
while ($s = fgets($fp)) :
    //if ($i > 5) break;
    //retrieve an associate array from the file pointer    
    $result[] = unpack($sRmsConfiguration,$s);
    $i++;
endwhile;
fclose($fp) or die("can't close file");
// extract the header row, then apply callback and re-add the header
$aHeaderRow = array_shift($result);
 array_walk_recursive(
    $result,
    function(&$value, $key)
    {                 
        $value = trim($value);
        switch($GLOBALS['aRmsConfiguration'][$key]['conversion']) :
            case 'int':
                $value = (int) trim($value);
                break;
            case 'float':
                $value = (float) trim($value);
                break;
            case 'datetime':
                $value = DateTime::createFromFormat('d m Y H:i:s', trim($value), new DateTimeZone('UTC'));
                break;
            case 'CertificateType':
                $value = strtolower('value') === 'intl' ? 'I' : 'C';
            case 'SailNumber':
                $value = $value;
            default:
                $value = empty($value) ? null : trim($value);
                break;
        endswitch;
    }
);
array_unshift($result, $aHeaderRow);
foreach ($result as $key => $value) {
    echo $value['SailNumber']."\r\n";
    
}
?>