<?php
/**
 * Classe para integração do módulo agenda com sites externos.
 * 
 * @empresa Polis Tecnologia
 * @author Gregori Maus <gregori@polistecnologia.com.br>
 * 
 * Repositório no github : git@github.com:PolisTecnologia/PJSON.git
 */
 
class Pjson {

   // Variável privada para a url
   private $url;
   
   // Mensagem de erro, caso seja necesária para validação
   private $errMsg = null;
   
   // Variável que determina o padrão de trabalho das informações obtidas pela função _JOpen() 
   // se nula realiza o padrão da função
   public  $formato = null;
   
   
   
   // Função responsável por receber a url que contém o arquivo json
   public function GetUrl($url){
       
	  // Realiza a validação do formato da url através da função ValidateUrl()
      if(!$this->ValidateUrl($url)){
        
		// Atribui um valor a mensagem que será exibida caso exiata um erro na url apresentada
        $this->errMsg = "Erro ao validar url. <br> Por favor verifique o endereço que você está inserindo.";
        
      }else{
          
		  // Caso contrário atribui à variável privada $url a url apresentada
          $this->url = $url;
          
      } 
       
       
   }
   
   

    /* Função que abre os arquivos json e os lê */
    public function _jOpen(){
        
        
		// Verifica se existe algum erro na url, não havendo segue a função
        if($this->errMsg == null){
            
                $url = $this->url;

                //Criando a url para o aquivo json
                $jsonurl = $url;

                //Retorna o conteudo do arquivo em formato de string
                $json = file_get_contents($jsonurl,0,null,null);

                //Decodificando a string e criando o json
                $json_output = json_decode($json);
				
					/* Verifica se foi atribuído algum valor a variável $formato
					 * Caso isto não tenha ocorrido retorna um código html com o padrão definido pela classe
					 * através da função GeraTabela() 
					 */
					if($this->formato == null){ 
						
						
						// Utiliza a função GeraTabela() para retornar um código html padrão de resposta
						$this->GeraTabela($json_output);						
						
						
					}else{ 
						
						// Caso seja atribuído valor à variável $formato, apenas retorna os dados obtidos no json
						return ($json_output);
					}
                
            
            
        }else{
            
			// Caso exista erro na url imprime na tela a mensagem de erro.
            echo $this->errMsg;
            
        }    


    }
    
    
	// Função protegida responsável por criar padrão básico para geração de código html com 
	// as informações do json
    protected function GeraTabela($param){
        
		// Variável usada para comparar datas
        $datacompare = null; 
					
					    // Atribui código css padrão para o código html e inicializa a variável html
                        $html = "<style>
                            .polis
                            {
                                width:100%; 
                                font-size: 11pt;                                 
                            }
                            
                            table {border-collapse: collapse;}
                            
                                td {
                                        border: 0.5px solid #a5a5a5;
                                    }
                                
                        </style>";
        
        // Acrescenta uma tabela padrão 
        $html .= "<table border='0' class='polis' cellpadding='6' cellspacing='6'>
                    <tr><th>Data</th><th>Titulo</th><th>Onde</th></tr>";
		
		// Realiza um loop no documento varrendo as informações 
        foreach ( $param->eventos as $trend )
       {
	   
			// Variável responsável por comparar as datas, é atribuído a ela o valor inicial da um dos eventos
            $datacompareother = $this->InvertData($trend->start,'/');
						
						// Condição que compara a variável até agora nula $datacompare com a variável $datacompareother
						// que possui o valor da data incial, para agrupar eventos no mesmo dia
						if($datacompare != $datacompareother ){
							
								// Cria um titulo com a data 
								$html .= "<tr><td colspan='4' style='background:#eee'>".$this->InvertData($trend->start,'/')."</td></tr>";
							   
							   // acrescenta os eventos reativos a esta data
								$html .= "<tr>
											<td>".substr($trend->start,11,5)."</td>
											<td>".utf8_decode($trend->title)."</td>
											<td>".utf8_decode($trend->onde)."</td>
									  
										  </tr>";
								
								// Atribui o valor da data inicial a variável $datacompare
								$datacompare = $this->InvertData($trend->start,'/');
							
						   }else{
							   
							   // Acrescenta eventos na tabela se forem do mesmo dia
								$html .= "<tr>
											<td>".substr($trend->start,11,5)."</td>
											<td>".utf8_decode($trend->title)."</td>
											<td>".utf8_decode($trend->onde)."</td>
										   
										  </tr>"; 
								
								// Atribui o valor da data inicial a variável $datacompare
								$datacompare = $this->InvertData($trend->start,'/');							   
							   
						    }
       
       }
       
	   // Finaliza a variável $html
       $html .= "</table>";
       
	   // Imprime a variável $html gerada
       echo $html;          
        
        
    }


	// Função protegida para validar a url
    protected function ValidateUrl($url){
        
        return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
    
    }
    
    
	//Função protegida para inverter e apresentar de uma forma clara as datas
    protected function InvertData($datainv,$sep){
        
       $datainv = substr($datainv, 0,10);

       $ano=substr("$datainv",0, 4);
       $mes=substr("$datainv",5, 2);
       $dia=substr("$datainv",8, 2);
       $datainv="$dia$sep$mes$sep$ano";
       return $datainv;
       
       }



}
?>
