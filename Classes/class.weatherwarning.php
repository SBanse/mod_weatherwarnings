<?php

class weatherwarings{
    public $decodeData; 

    protected string $server;

    protected string $json;
    
    protected $warncellIDs = ['ort' => [], 'kreis' => []];

    protected $typeName = 'Warnungen_Gemeinden';

    protected $possibleTypeName = ['Warnungen_Gemeinden', 'Warnungen_Landkreise'];

    protected $WarnProperties = [];

    protected $CRS = 'CRS:84';

    protected array $errors = [];

    public function __construct(){
        //gemeinden
        $this->server = "https://maps.dwd.de/geoserver/dwd/ows?version=2.0.0&SERVICE=WFS";
        //&outputFormat=application/json&REQUEST=GetFeature&typeName=dwd:Warnungen_Gemeinden&LAYERS=dwd:Warnungen_Gemeinden&CRS=CRS:84";
    }

    public function getWarningDatas(){
        try{
            $data = $this->_getResponse();

            if(count($this->warncellIDs['kreis']) > 0){
                $this->typeName = 'Warnungen_Landkreise';
                $this->_getResponse('kreis');
            }

            return $this->WarnProperties;
        } catch(Exception $ex){
            return False;
        }
    }

    public function addWarnCellID(int $ID, string $art="ort"){
        $this->warncellIDs[$art][] = $ID;
    }

    public function setTypeName($name){
        if(in_array($name, $this->possibleTypeName)){
            $this->typeName = $name;
            return true;
        } else {
            $this->errors[] = 'typeName not possible';
            return false;
        }
    }

    public function getErrors(){
        return $this->errors;
    }

    protected function _getResponse(string $art='ort'){
        try{
            $url = $this->server .'&REQUEST=GetFeature&typeName=dwd:'.$this->typeName.'&Layers=dwd:'.$this->typeName.'&CRS='.$this->CRS;
            if($art == "ort" && count($this->warncellIDs[$art]) > 0) {
                $url .= '&CQL_FILTER=WARNCELLID%20IN%20('. implode(',', $this->warncellIDs[$art]) .')';
            } elseif($art == "kreis" && count($this->warncellIDs[$art]) > 0){
                $url .= '&CQL_FILTER=GC_WARNCELLID%20IN%20('. implode(',', $this->warncellIDs[$art]) .')';
            }         
            $url .= '&outputFormat=application%2Fjson';

            if(strlen($this->json = @file_get_contents($url)) == 0){
                throw new Exception('No data from weatherwarning server received');
            } else {
                $this->decodeData = json_decode($this->json, true);                
                $this->_structurWarndata($this->decodeData);
                return true;
            }
            
        } catch (Exception $ex){            
            $this->errors[] = $ex->getMessage();
            error_log($ex->getMessage());
            return false;
        }
    }

    protected function _structurWarnData(array $responsData){
        
        if(!empty($responsData['features'])){

            foreach($responsData['features'] as $feature => $values){
                
                $currentEvent   = ['EVENT' => '', 'ONSET' => '','EXPIRES' => '' ];

                foreach($values['properties'] as $propertie => $propertieValue){
                                        
                    switch($propertie){
                        case 'EVENT':
                            $event = $propertieValue;
                            $currentEvent['EVENT'] = $propertieValue;
                            break;
                        case 'NAME':
                            $region = $propertieValue;
                            break;                        
                        default:
                            $currentEvent[$propertie] = $propertieValue;
                            //var_dump($propertie,$propertieValue);
                    }
                }

                if(empty($this->WarnProperties[$event])){
                    $this->WarnProperties[$event] = $currentEvent;
                    $this->WarnProperties[$event]['regions'] = [];
                }

                if(!empty($region) 
                    && !in_array($region,$this->WarnProperties[$event]['regions'])) {
                    $this->WarnProperties[$event]['regions'][] = $region; 
                               
                }
            }
        }
    }
}