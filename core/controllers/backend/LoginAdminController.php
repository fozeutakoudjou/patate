<?php
namespace core\controllers\backend;
use core\generator\html\HtmlGenerator;
use core\Validate;
use core\Tools;
use core\models\User;
class LoginAdminController extends AdminController
{
	protected $layout = 'login/layout';
	protected $header = 'login/header';
	protected $footer = 'login/footer';
	protected $defaultAction = 'login';
    public function __construct()
    {
        $this->bootstrap = true;
        $this->errors = array();
        $this->useOfHeader = false;
        $this->useOfFooter = false;
        $this->metaTitle = $this->l('Administration panel');
        $this->css_files = array();
        parent::__construct();

        if (!headers_sent()) {
            header('Login: true');
        }
    }

    /*public function setMedia()
    {
        $this->addJquery();
        $this->addjqueryPlugin('validate');
        $this->addJS(_PS_JS_DIR_.'jquery/plugins/validate/localization/messages_'.$this->context->language->iso_code.'.js');
        $this->addCSS(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/css/admin-theme.css', 'all', 0);
        $this->addCSS(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/css/overrides.css', 'all', PHP_INT_MAX);
        $this->addJS(_PS_JS_DIR_.'vendor/spin.js');
        $this->addJS(_PS_JS_DIR_.'vendor/ladda.js');
    }*/
	
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
		$this->form = $this->generator->createForm(true, false, '#', true, $this->l('Login'), '', $formAction, 'submitLogin'/*, $this->l('Email or password are incorrect')*/);
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
		$this->form->addChild($this->generator->createCheckbox('stay_logged_in', 'Stay logged in', true));
		$this->form->addChild($passwordLink);
		
		$this->formPassword->addChild($inputEmail);
		$this->generator->setAsShowHide($this->formPassword->getCancel(), '.form_login', '.form_forget_password');
	}
	
	public function checkUserAccess($action)
    {
		return true;
    }

    public function initContent()
    {
        /*if (!Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) {
            // You can uncomment these lines if you want to force https even from localhost and automatically redirect
            // header('HTTP/1.1 301 Moved Permanently');
            // header('Location: '.Tools::getShopDomainSsl(true).$_SERVER['REQUEST_URI']);
            // exit();
            $clientIsMaintenanceOrLocal = in_array(Tools::getRemoteAddr(), array_merge(array('127.0.0.1'), explode(',', Configuration::get('PS_MAINTENANCE_IP'))));
            // If ssl is enabled, https protocol is required. Exception for maintenance and local (127.0.0.1) IP
            if ($clientIsMaintenanceOrLocal) {
                $warningSslMessage = Tools::displayError('SSL is activated. However, your IP is allowed to enter unsecure mode for maintenance or local IP issues.');
            } else {
                $url = 'https://'.Tools::safeOutput(Tools::getServerName()).Tools::safeOutput($_SERVER['REQUEST_URI']);
                $warningSslMessage = sprintf(
                    Translate::ppTags(
                        Tools::displayError('SSL is activated. Please connect using the following link to [1]log into secure mode (https://)[/1]', false),
                        array('<a href="%s">')
                    ),
                    $url
                );
            }
            $this->context->smarty->assign('warningSslMessage', $warningSslMessage);
        }

        if (file_exists(_PS_ADMIN_DIR_.'/../install')) {
            $this->context->smarty->assign('wrong_install_name', true);
        }

        if (basename(_PS_ADMIN_DIR_) == 'admin' && file_exists(_PS_ADMIN_DIR_.'/../admin/')) {
            $rand = 'admin'.sprintf('%03d', rand(0, 999)).Tools::strtolower(Tools::passwdGen(6)).'/';
            if (@rename(_PS_ADMIN_DIR_.'/../admin/', _PS_ADMIN_DIR_.'/../'.$rand)) {
                Tools::redirectAdmin('../'.$rand);
            } else {
                $this->context->smarty->assign(array(
                    'wrong_folder_name' => true
                ));
            }
        } else {
            $rand = basename(_PS_ADMIN_DIR_).'/';
        }

        $this->context->smarty->assign(array(
            'randomNb' => $rand,
            'adminUrl' => Tools::getCurrentUrlProtocolPrefix().Tools::getShopDomain().__PS_BASE_URI__.$rand
        ));

        // Redirect to admin panel
        if (Tools::isSubmit('redirect') && Validate::isControllerName(Tools::getValue('redirect'))) {
            $this->context->smarty->assign('redirect', Tools::getValue('redirect'));
        } else {
            $tab = new Tab((int)$this->context->employee->default_tab);
            $this->context->smarty->assign('redirect', $this->context->link->getAdminLink($tab->class_name));
        }

        if ($nb_errors = count($this->errors)) {
            $this->context->smarty->assign(array(
                'errors' => $this->errors,
                'nbErrors' => $nb_errors,
                'shop_name' => Tools::safeOutput(Configuration::get('PS_SHOP_NAME')),
                'disableDefaultErrorOutPut' => true,
            ));
        }

        if ($email = Tools::getValue('email')) {
            $this->context->smarty->assign('email', $email);
        }
        if ($password = Tools::getValue('password')) {
            $this->context->smarty->assign('password', $password);
        }

        $this->setMedia();
        $this->initHeader();
        parent::initContent();
        $this->initFooter();

        //force to disable modals
        $this->context->smarty->assign('modals', null);*/
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
			$loadedUser = User::getAdminByEmail($email, $password);
			$user  = ($loadedUser == null) ? $this->context->getUser() : $loadedUser;
            $this->context->employee = new Employee();
            $is_employee_loaded = $this->context->employee->getByEmail($email, $passwd);
            $employee_associated_shop = $this->context->employee->getAssociatedShops();
            if ($loadedUser == null) {
                $this->errors[] = $this->l('The user does not exist, or the password provided is incorrect.');
                $user->logout();
            }else{
				
				//Make log
                //PrestaShopLogger::addLog(sprintf($this->l('Back Office connection from %s', 'AdminTab', false, false), Tools::getRemoteAddr()), 1, null, '', 0, true, (int)$this->context->employee->id);

                $this->context->employee->remote_addr = (int)ip2long(Tools::getRemoteAddr());
                // Update cookie
                $cookie = Context::getContext()->cookie;
                $cookie->id_employee = $this->context->employee->id;
                $cookie->email = $this->context->employee->email;
                $cookie->profile = $this->context->employee->id_profile;
                $cookie->passwd = $this->context->employee->passwd;
                $cookie->remote_addr = $this->context->employee->remote_addr;

                if (!Tools::getValue('stay_logged_in')) {
                    $cookie->lastActivity = time();
                }

                $cookie->write();

                // If there is a valid controller name submitted, redirect to it
                /*if (isset($_POST['redirect']) && Validate::isControllerName($_POST['redirect'])) {
                    $url = $this->context->link->getAdminLink($_POST['redirect']);
                } else {
                    $tab = new Tab((int)$this->context->employee->default_tab);
                    $url = $this->context->link->getAdminLink($tab->class_name);
                }*/

                $this->redirectAfter = true;
            }
        }else{
			$this->form->setErrors($this->formErrors);
			$this->form->setValue(array('email'=>$email));
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
			
		}else{
			$this->formPassword->setErrors($this->formErrors);
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
