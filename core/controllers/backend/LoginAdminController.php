<?php
namespace core\controllers\backend;
use core\generator\html\HtmlGenerator;
use core\Validate;
use core\Tools;
use core\StringTools;
use core\models\User;
use core\models\Configuration;
class LoginAdminController extends AdminController
{
	protected $layout = 'login/layout';
	protected $header = 'login/header';
	protected $footer = 'login/footer';
	protected $defaultAction = 'login';
	protected $useMenu = false;
    public function __construct()
    {
        $this->useOfHeader = false;
        $this->useOfFooter = false;
        $this->metaTitle = $this->l('Administration panel');
        parent::__construct();

        if (!headers_sent()) {
            header('Login: true');
        }
		$this->availableActions[$this->defaultAction] = null;
    }
	
	protected function processLogin()
    {
		$this->createForm();
		$this->formPassword->setVisible(false);
		$submitted = false;
		if (Tools::isSubmit($this->form->getSubmitAction())) {
            $this->doConnect();
			$submitted = true;
        } elseif (Tools::isSubmit($this->formPassword->getSubmitAction())) {
            $this->doForgot();
			$submitted = true;
        }
		if(!$submitted){
			if(isset($_GET['email'])){
				$this->form->setValue(array('email'=>$_GET['email']));
				unset($_GET['email']);
			}
			$redirectParams = array();
			foreach($_GET as $key => $value){
				if(strpos($key, self::REDIRECT_PREFIX)===0){
					$redirectParams[StringTools::strReplaceOnce(self::REDIRECT_PREFIX, '', $key)] = $value;
				}
			}
			$this->form->setChildValue('redirectData', http_build_query($redirectParams));
		}
		if(!$submitted || $this->hasErrors() || Tools::isSubmit($this->formPassword->getSubmitAction())){
			$this->createFormFields();
			$this->processResult['content'] = $this->form->generate().$this->formPassword->generate();
		}
    }
	
	protected function createForm($update = false)
    {
		$this->generator->setDefaultCancelText($this->l('Back'));
		$this->generator->setDefaultSubmitText($this->l('Submit'));
		$this->generator->setDefaultSubmitIcon('');
		$this->generator->setDefaultCancelIcon('');
		
		$formAction = $this->createUrl();
		$this->form = $this->generator->createForm(true, false, '#', true, $this->l('Login'), '', $formAction, 'submitLogin');
		$this->formPassword = $this->generator->createForm(true, true, '#', true, $this->l('Forget Password ?'), '', $formAction, 'submitForgot', '', $this->l('Enter your e-mail to reset it.'));
		$this->formPassword->addClass('form_forget_password');
		$this->formPassword->setTemplateFile('login/generator/form_forget_password', false);
    }
	
	protected function createFormFields($update = false)
    {
		$inputEmail = $this->generator->createTextField('email', $this->l('Email'));
		$inputEmail->setTemplateFile('login/generator/input_text', false);
		$inputEmail->setLeftIcon($this->generator->createIcon('envelope', false));
		$inputPassword = $this->generator->createPasswordField('password', $this->l('Password'));
		$inputPassword->setTemplateFile('login/generator/input_text', false);
		$inputPassword->setLeftIcon($this->generator->createIcon('lock', false));
		
		$passwordLink = $this->generator->createLink($this->l('Forgot Password?'), '#', '',$this->l('Forgot Password?'), false,  'forget_password');
		$this->generator->setAsShowHide($passwordLink, '.form_forget_password', '.form_login');
		$this->form->setTemplateFile('login/generator/form', false);
		$this->form->addClass('form_login');
		$this->form->addChild($inputEmail);
		$this->form->addChild($inputPassword);
		$this->form->addChild($this->generator->createCheckbox('stay_logged_in', 'Stay logged in', true, '1'));
		$this->form->addChild($this->generator->createHiddenInput('redirectData'));
		$this->form->addChild($passwordLink);
		
		$this->formPassword->addChild(clone $inputEmail);
		$this->generator->setAsShowHide($this->formPassword->getCancel(), '.form_login', '.form_forget_password');
	}
	
	public function checkUserAccess($action, $idWrapper = null)
    {
		return true;
    }

    public function checkToken()
    {
        return true;
    }

    public function doConnect()
    {
        /* Check fields validity */
        $password = trim(Tools::getValue('password'));
        $email = trim(Tools::getValue('email'));
        if (empty($email)) {
            $this->formErrors['email'] = $this->l('Email is empty.');
        } elseif (!Validate::isEmail($email)) {
            $this->formErrors['email']  = $this->l('Invalid email address.');
        }

        if (empty($password)) {
            $this->formErrors['password'] = $this->l('The password field is blank.');
        } elseif (!Validate::isPassword($password)) {
            $this->formErrors['password'] = $this->l('Invalid password.');
        }

        if (!$this->hasErrors()) {
			$user = User::getAdminByEmail($email, $password);
			if ($user == null) {
                $this->errors[] = $this->l('The user does not exist, or the password provided is incorrect.');
                $this->context->getUser()->logout();
            }else{
				$remoteAddress = Tools::getRemoteAddress();
				//Make log
                //PrestaShopLogger::addLog(sprintf($this->l('Back Office connection from %s', 'AdminTab', false, false), Tools::getRemoteAddr()), 1, null, '', 0, true, (int)$this->context->employee->id);

                // Update cookie
                $cookie = $this->context->getCookie();
                $cookie->idUser = $user->getId();
                $cookie->email = $user->getEmail();
                $cookie->password = $user->getPassword();;
                $cookie->remoteAddress = Tools::getNumericRemoteAddress($remoteAddress);

                if (!Tools::getValue('stay_logged_in')) {
                    $cookie->lastActivity = time();
                }

                $cookie->write();
				parse_str($_POST['redirectData'], $redirectData);
                // If there is a valid controller name submitted, redirect to it
                if (isset($redirectData) && isset($redirectData['controller']) && Validate::isControllerName($redirectData['controller'])) {
					$controller = $redirectData['controller'];
					unset($redirectData['controller']);
                } else {
					$controller = 'index';
                }
				$module = '';
				if(isset($redirectData['module'])){
					$module = $redirectData['module'];
					unset($redirectData['module']);
				}
				$redirectData = is_array($redirectData) ? $redirectData : array();
                $this->redirectAfter = true;
				$this->redirectLink = $this->context->getLink()->getAdminLink($module, $controller, $redirectData);
			}
        }else{
			$this->form->setErrors($this->formErrors);
		}
		if($this->hasErrors()){
			$this->form->setValue(array('email'=>$email, 'redirectData'=>$_POST['redirectData']));
		}
    }

    public function doForgot()
    {
        $email = trim(Tools::getValue('email'));
        if (empty($email)) {
            $this->formErrors['email'] = $this->l('Email is empty.');
        } elseif (!Validate::isEmail($email)) {
            $this->formErrors['email']  = $this->l('Invalid email address.');
        }

        if (!$this->hasErrors()) {
			$user = User::getAdminByEmail($email);
			if ($user == null) {
                $this->errors[] = $this->l('This account does not exist.');
            } elseif ((strtotime($user->getLastPasswordGeneratedTime().'+'.Configuration::get('PASSWORD_TIME_BACK').' minutes') - time()) > 0) {
                $this->errors[] = sprintf(
                    $this->l('You can regenerate your password only every %d minute(s)'),
                    Configuration::get('PASSWORD_TIME_BACK')
                );
            }
		}else{
			$this->formPassword->setErrors($this->formErrors);
		}
		if($this->hasErrors()){
			$this->formPassword->setValue(array('email'=>$email));
			$this->formPassword->setVisible(true);
			$this->form->setVisible(false);
		}
        /*if (_PS_MODE_DEMO_) {
            $this->errors[] = Tools::displayError('This functionality has been disabled.');
        } elseif (!($email = trim(Tools::getValue('email_forgot')))) {
            $this->errors[] = Tools::displayError('Email is empty.');
        } elseif (!Validate::isEmail($email)) {
            $this->errors[] = Tools::displayError('Invalid email address.');
        } else {
            $employee = new Employee();
            if (!$employee->getByEmail($email) || !$employee) {
                $this->errors[] = Tools::displayError('This account does not exist.');
            } elseif ((strtotime($employee->last_passwd_gen.'+'.Configuration::get('PS_PASSWD_TIME_BACK').' minutes') - time()) > 0) {
                $this->errors[] = sprintf(
                    Tools::displayError('You can regenerate your password only every %d minute(s)'),
                    Configuration::get('PS_PASSWD_TIME_BACK')
                );
            }
        }

        if (!count($this->errors)) {
            $pwd = Tools::passwdGen(10, 'RANDOM');
            $employee->passwd = Tools::encrypt($pwd);
            $employee->last_passwd_gen = date('Y-m-d H:i:s', time());

            $params = array(
                '{email}' => $employee->email,
                '{lastname}' => $employee->lastname,
                '{firstname}' => $employee->firstname,
                '{passwd}' => $pwd
            );

            if (Mail::Send($employee->id_lang, 'employee_password', Mail::l('Your new password', $employee->id_lang), $params, $employee->email, $employee->firstname.' '.$employee->lastname)) {
                // Update employee only if the mail can be sent
                Shop::setContext(Shop::CONTEXT_SHOP, (int)min($employee->getAssociatedShops()));

                $result = $employee->update();
                if (!$result) {
                    $this->errors[] = Tools::displayError('An error occurred while attempting to change your password.');
                } else {
                    die(Tools::jsonEncode(array(
                        'hasErrors' => false,
                        'confirm' => $this->l('Your password has been emailed to you.', 'AdminTab', false, false)
                    )));
                }
            } else {
                die(Tools::jsonEncode(array(
                    'hasErrors' => true,
                    'errors' => array(Tools::displayError('An error occurred while attempting to change your password.'))
                )));
            }
        } elseif (Tools::isSubmit('ajax')) {
            die(Tools::jsonEncode(array('hasErrors' => true, 'errors' => $this->errors)));
        }*/
    }
}
