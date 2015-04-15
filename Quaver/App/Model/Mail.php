<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Model;

/**
 * Mail class
 * @package App
 */
class Mail
{

    private $type; // "mandril"

    public $action;

    public $subject,
        $subjectVars, // dinamic content to subject
        $template,
        $templateVars, // dinamic content to email body
        $body,
        $to,
        $toName,
        $from,
        $fromName,
        $htmlMode = true,
        $attachments,
        $language;

    /**
     * Mail constructor
     * @param type $type 
     * @return type
     */
    public function __construct($type = '')
    {
        if (isset($type)){
            $this->type = $type;
        }
    }


    /**
     * Configure email
     * @return type
     */
    public function prepare()
    {

        // Set language
        if (!isset($this->language)) {
            if (isset($GLOBALS['_lang'])) {
                $this->language = $GLOBALS['_lang']->id;    
            }
        }

        // Render subject and body
        if (isset($this->template)) {

            if ($this->htmlMode === true) {

                // Start twig env
                $loader = new \Twig_Loader_String();
                $twig_options = array();
                $twig_options['autoescape'] = false;
                $twig = new \Twig_Environment($loader, $twig_options);

                $template = htmlspecialchars_decode($this->template, ENT_QUOTES);
                
                if (isset($this->templateVars)) {
                    // Render twig template
                    $this->body = $twig->render($template, $this->templateVars);    
                } else {
                    $this->subject = sprintf($this->subject, $this->subjectVars);
                }
                
                
            } else {
                $this->body = $this->template;
                $this->subject = sprintf($this->subject, $this->subjectVars);
            }

            $this->body = html_entity_decode($this->body);

            if (empty($this->from)) {
                $this->from = CONTACT_EMAIL;
            }

            if (empty($this->fromName)) {
                $this->fromName = CONTACT_NAME;
            }

            $this->send();

        }

    }

    /**
     * Send email
     * @return type
     */
    public function send()
    {   
        // Check if mail enabled to block send
        if (defined('MAIL_ENABLED') && MAIL_ENABLED === true) {
            
            $return = false;      
     
            //Check type         
            if ($this->type == 'mandrill'){

                if (defined('MANDRILL') && MANDRILL === true){

                    try {
                        $mandrill = new \Mandrill(MANDRILL_APIKEY);

                        if (!empty($_attachments)) {
                            $message = array(
                                'html' => $this->body,
                                'subject' => $this->subject,
                                'from_email' => $this->from,
                                'from_name' => $this->fromName,
                                'to' => array(
                                    array(
                                        'email' => $this->to,
                                        'name' => $this->toName,
                                        'type' => 'to'
                                    )
                                ),
                                "attachments" => array(
                                    array(
                                        'content' => $this->attachments['file_base64'],
                                        'type' => $this->attachments['type'], // for example application/pdf
                                        'name' => $this->attachments['name'],
                                    ),
                                )
                            );
                        } else {
                            $message = array(
                                'html' => $this->body,
                                'subject' => $this->subject,
                                'from_email' => $this->from,
                                'from_name' => $this->fromName,
                                'to' => array(
                                    array(
                                        'email' => $this->to,
                                        'name' => $this->toName,
                                        'type' => 'to'
                                    )
                                )
                            );
                        }
                        
                        $async = false;
                        $return = $mandrill->messages->send($message, $async);
                        
                    } catch(\Mandrill_Error $e) {

                        // Mandrill errors are thrown as exceptions
                        throw new \Quaver\Core\Exception('A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage());
                        
                    }

                    return $return;

                }  
                
            } else {
                
                $mail = new \PHPMailer;

                $mail->From = $this->from;
                $mail->FromName = $this->fromName;
                $mail->addAddress($this->to, $this->toName); // Add a recipient

                $mail->isHTML(true); // Set email format to HTML

                $mail->Subject = $this->subject;
                $mail->Body    = $this->body;
                
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
