<? use Phalcon\Tag; ?>

<h2>Зарегестрируйся, используя форму ниже</h2>

<?= Tag::form('signup/register') ?>

<p>
	<label for="name">Имя</label>
	<?= Tag::textField('name') ?>
</p>

<p>
	<label for="name">e-mail</label>
	<?= Tag::textField('email') ?>
</p>
<p>
	<?= Tag::submitButton('Регистрация') ?>
</p>
</form>