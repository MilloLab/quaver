<?php

namespace Quaver\App\Mail;

use Quaver\Model\Mailcenter;
use Quaver\Core\Config;

class BaseMail implements \Quaver\Core\MailInterface
{
    protected $vars;

    public function __construct()
    {
        $this->vars = [];
        $this->subjectVars = [];
    }

    public function set($key, $value)
    {
        $this->vars[$key] = $value;
    }

    public function renderSubject($subject)
    {
        if (empty($subject)) {
            throw new \UnexpectedValueException('Render subject mail with empty title');
        }

        return $subject;
    }

    public function renderBody($template)
    {
        if (empty($template)) {
            throw new \UnexpectedValueException('Render body mail with empty body');
        }

        // Start twig env
        $twigTemplate = array('template' => $template);
        $loader = new \Twig_Loader_Array($twigTemplate);
        $twig_options = array();
        $twig_options['autoescape'] = false;
        $twig = new \Twig_Environment($loader, $twig_options);

        // Render twig template
        $qv = array(
            'url' => array(
                'host' => Config::get('mail.URL_HOST', 'www.domain.com'),
                'protocol' => Config::get('mail.URL_PROTOCOL', 'http://'),
            ),
        );
        $qv['url']['absolute'] = $qv['url']['protocol'].$qv['url']['host'];

        return $twig->render('template', array('qv' => $qv) + $this->getBodyVars());
    }

    public function getAttachments()
    {
        return [];
    }

    public function getReplyTo()
    {
        return null;
    }

    public function onSent(Mailcenter $mail)
    {
    }

    /**
     * getBodyVars.
     *
     * @return array
     *
     * @throws UnexpectedValueException
     */
    protected function getBodyVars()
    {
        return $this->vars;
    }
}
