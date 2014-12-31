<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Model;

class Mail
{

    public $type; // "mandril"

    /**
     * constructor
     * @param type $type 
     * @return type
     */
    public function __construct($type = '')
    {
        if ( isset($type) && !empty($type)){
            $this->type = $type;
        }
    }

    /**
     * send function
     * @param type $_subject 
     * @param type $_template 
     * @param type $_to 
     * @param type $_toName 
     * @param type $_vars 
     * @param type $_language 
     * @param type $_subject_variables
     * @param type $_htmlMode 
     * @param type $_attachments 
     * @return type
     */
    public function send($_subject, $_template = '', $_to, $_toName = '', $_from = '', $_fromName = '', $_vars = array(), $_language = '', $_subject_variables = array(), $_htmlMode = true, $_attachments = array())
    {
        global $_lang;
        
        // Check dev mode to block send
        if (defined('DEV_MODE') && DEV_MODE == false){
            
            $return = false;

            // Start array mail
            $_mail = array();


            if (!empty($_template)){

                if ($_htmlMode === true){
                    // Start twig env
                    $loader = new \Twig_Loader_String();
                    $twig_options = array();
                    $twig_options['autoescape'] = false;
                    $twig = new \Twig_Environment($loader, $twig_options);

                    $_template = htmlspecialchars_decode($_template, ENT_QUOTES);

                    // Render twig template
                    $html = $twig->render($_template, $_vars);
                } else {
                    $html = $_template;    
                }

                
            }

            // Check language
            if (empty($_language)){
                $_language = $_lang->id;
            }
            
            // Check subject vars
            if (!empty($_subject_variables)){
                // Custom subjects by language and vars
                $_subject = sprintf($_subject, $_subject_variables);        
            }

            $_mail['to'] = $_to;
            $_mail['toName'] = $_toName;
            if (!empty($_from)){
                $_mail['from'] = $_from;
            }
            if (!empty($_fromName)){
                $_mail['fromName'] = $_fromName;
            }            
            $_mail['body'] = html_entity_decode($html);
            $_mail['subject'] = $_subject;

                
            //Check type         
            if ($this->type == 'mandrill'){

                if (defined('MANDRILL') && MANDRILL == true){

                    try {
                        $mandrill = new \Mandrill(MANDRILL_APIKEY);

                        if (!empty($_attachments)) {
                            $message = array(
                                'html' => $_mail['body'],
                                'subject' => $_mail['subject'],
                                'from_email' => $_mail['from'],
                                'from_name' => $_mail['fromName'],
                                'to' => array(
                                    array(
                                        'email' => $_mail['to'],
                                        'name' => $_mail['toName'],
                                        'type' => 'to'
                                    )
                                ),
                                "attachments" => array(
                                    array(
                                        'content' => $_attachments['file_base64'],
                                        'type' => $_attachments['type'], // for example application/pdf
                                        'name' => $_attachments['name'],
                                    ),
                                )
                            );
                        } else {
                            $message = array(
                                'html' => $_mail['body'],
                                'subject' => $_mail['subject'],
                                'from_email' => $_mail['from'],
                                'from_name' => $_mail['fromName'],
                                'to' => array(
                                    array(
                                        'email' => $_mail['to'],
                                        'name' => $_mail['toName'],
                                        'type' => 'to'
                                    )
                                )
                            );
                        }
                        
                        $async = false;
                        $return = $mandrill->messages->send($message, $async);
                        
                    } catch(\Mandrill_Error $e) {
                        // Mandrill errors are thrown as exceptions
                        echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
                        // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
                        throw $e;
                    }

                    return $return;

                }  
                
            } else {
                
                $mail = new \PHPMailer;

                $mail->From = $_mail['from'];
                $mail->FromName = $_mail['fromName'];
                $mail->addAddress($_mail['to'], $_mail['toName']);     // Add a recipient

                $mail->isHTML(true);                                  // Set email format to HTML

                $mail->Subject = $_mail['subject'];
                $mail->Body    = $_mail['body'];
                
                try {
                    $return = $mail->Send();
                } catch (\phpmailerException $e) {
                    echo $e->errorMessage();
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
        
                return $return;         

            }

    
                        
        }
    
    }
}
