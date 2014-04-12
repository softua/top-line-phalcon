<? 

class SignupController extends BaseController
{
	public function indexAction()
	{
		
	}

	public function registerAction()
	{
		$user = new Users();

		// Сохраняем и проверяем на наличие ошибок
		$success = $user->save($this->request->getPost(), ['name', 'email']);

		if ($success)
			echo 'Спасибо за регистрацию!';
		else {
			echo 'К сожалению, возникли следующие проблемы: <br/>';
			foreach ($user->getMessages() as $message) {
				echo $message->getMessage(), '<br/>';
			}
		}

		$this->view->disable();
	}
}