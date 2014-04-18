<?php
/**
 * Created by PhpStorm.
 * User: Ali Rajabi - www.alirajabi.com
 * Date: 11/18/13
 * Time: 12:20 PM
 */
use \rajabi\helper\RFile as RFile;
use \rajabi\helper\RException as RException;

class SwiftMailer extends CApplicationComponent
{

    public $logTable = 'mailer_log';

    public $log = false;

    public $charset = 'utf-8';

    public $defaultFrom;

    public $defaultTo;

    public $transport = 'mail';

    public $sendMailCommand = '/usr/sbin/sendmail -bs';

    public $smtpHost;

    public $smtpPort = 25;

    public $smtpEncryption = NULL;

    public $smtpUsername;

    public $smtpPassword;

    private $_messageInstance = null;

    public function send()
    {
        if (!$this->getMessage())
            throw new CException(t('error', 'pleaseSetMessageFirst'));

        $message = $this->_messageInstance;
        if (!$message->getFrom())
            $message->setFrom($this->defaultFrom);
        if (!$message->getTo())
            $message->setTo($this->defaultTo);

        $transportInstance = $this->getTransportInstance($this->transport);
        $mailer = Swift_Mailer::newInstance($transportInstance);
        $success = $mailer->send($message, $failure);

        //Log
        if (defined('YII_DEBUG') && YII_DEBUG)
            Yii::log(Yii::t('app', '{send} email(s) have been sent successfully and {failed} email(s) have been failed', array('{send}' => $success, '{failed}' => count($failure))));

        if ($this->log && $this->logTable) {
            app()->db->createCommand('CREATE TABLE IF NOT EXISTS `' . $this->logTable . '` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `messageId` int(11) NOT NULL,
               `returnPath` varchar(45) DEFAULT NULL,
               `fromAddress` varchar(45) NOT NULL,
               `sender` varchar(45) DEFAULT NULL,
               `toAddress` text NOT NULL,
               `cc` text,
               `bcc` text,
               `replyTo` varchar(45) DEFAULT NULL,
               `subject` varchar(45) DEFAULT NULL,
               `body` text,
               `sent` int(11) DEFAULT 0,
               `failure` text DEFAULT NULL,
               `cDate` int(11) NOT NULL,
               PRIMARY KEY(`id`)
             ) ENGINE = ARCHIVE DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1')
                ->execute();

            app()->db->createCommand()
                ->insert($this->logTable,
                    array(
                        'messageId' => $message->getId(),
                        'returnPath' => $message->getReturnPath(),
                        'fromAddress' => is_array($message->getFrom()) ? $this->arrayToString($message->getFrom()) : NULL,
                        'sender' => $message->getSender(),
                        'toAddress' => is_array($message->getTo()) ? $this->arrayToString($message->getTo()) : NULL,
                        'cc' => is_array($message->getCc()) ? $this->arrayToString($message->getCc()) : NULL,
                        'bcc' => is_array($message->getBcc()) ? $this->arrayToString($message->getBcc()) : NULL,
                        'replyTo' => $message->getReplyTo(),
                        'subject' => $message->getSubject(),
                        'body' => $message->getBody(),
                        'sent' => $success,
                        'failure' => array_filter($failure) ? $this->arrayToString($failure) : NULL,
                        'cDate' => $message->getDate()
                    ));
        }

        return $success;

    }

    public function setMessage()
    {
        if (!defined(('APP_MAILER_LOADED'))) {
            require_once(Yii::getPathOfAlias('vendor.swift.lib') . DS . 'swift_required.php');
            //Set startup SwiftMailer
            Swift_Preferences::getInstance()->setCharset($this->charset)
                ->setTempDir(RUNTIME_FOLDER . DS . 'mail');
            define('APP_MAILER_LOADED', true);

        }
        if ($this->_messageInstance)
            throw new CException(t('error', 'duplicateNewMessage'));
        return $this->_messageInstance = Swift_Message::newInstance()->setContentType('text/html');
    }

    public function getMessage()
    {
        return $this->_messageInstance;
    }

    public function getTransportInstance($transport)
    {
        switch ($transport) {
            case 'smtp':
                return Swift_SmtpTransport::newInstance()
                    ->setHost($this->smtpHost)
                    ->setPort($this->smtpPort)
                    ->setEncryption($this->smtpEncryption)
                    ->setUsername($this->smtpUsername)
                    ->setPassword($this->smtpPassword);
                break;
            case 'sendMail':
                return Swift_SendmailTransport::newInstance($this->sendMailCommand);
                break;
            case 'mail':
                return Swift_MailTransport::newInstance();
                break;
            default:

        }
    }

    public function init()
    {
        if (defined('APP_MAILER'))
            return TRUE;

        if (!$this->defaultFrom)
            throw new CException(t('error', 'pleaseSetFromEmailAddress'));

        if (!$this->defaultTo)
            throw new CException(t('error', 'pleaseSetToEmailAddress'));

        if ($this->transport === 'smtp') {
            if (!function_exists('proc_open'))
                throw new CException(t('error', 'proc_openFunctionDoesNotExist'));

            if (!$this->smtpHost || !$this->smtpUsername || !$this->smtpPassword)
                throw new CException(t('error', 'pleaseSetSmtpOptions'));
        }

        define('APP_MAILER', true);

        //Set mail path
        RFile::makeDir(RUNTIME_FOLDER . DS . 'mail');
        if (RException::get())
            RException::trace();

        parent::init();
    }

    private function arrayToString($arr)
    {
        if (!is_array($arr))
            return $arr;
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $temp[] = $this->arrayToString($val);
            } else {
                $temp[] = $key . ($val ? '=' . $val : '');
            }
        }
        return join('|', $temp);
    }
}
