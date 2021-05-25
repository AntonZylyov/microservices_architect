<h1>Пользователи</h1>
<hr>

<div class="container">
	<div class="row row-cols-3">
<?if (!empty($users)):
	foreach ($users as $user): ?>
	<div class="col">
		<div class="card" style="margin-bottom: 15px">
			<div class="card-body">
				<h5 class="card-title">#<?=$user['id']?> <?=$user['firstName']?> <?=$user['lastName']?></h5>
				<h6 class="card-subtitle mb-2 text-muted"><?=$user['username']?></h6>
				<p class="card-text">
					Email: <?=$user['email']?><br>
					Phone: <?=$user['phone']?>
				</p>
				<a href="#" class="card-link" data-toggle="modal" data-target="#editUser_<?=$user['id']?>">Изменить</a>
				<a href="<?=$baseUrl?>/delete_user/?id=<?=$user['id']?>" class="card-link">Удалить</a>
			</div>
		</div>
		<div class="modal fade" id="editUser_<?=$user['id']?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Редактирование пользователя</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<form action="<?=$baseUrl?>/edit_user/" method="post">
						<input type="hidden" name="id" value="<?=$user['id']?>">
						<div class="modal-body">
							<div class="form-group">
								<label>Username</label>
								<input type="text" class="form-control" name="username" value="<?=htmlspecialchars($user['username'])?>">
							</div>
							<div class="form-group">
								<label>First name</label>
								<input type="text" class="form-control" name="firstName" value="<?=htmlspecialchars($user['firstName'])?>">
							</div>
							<div class="form-group">
								<label>Last name</label>
								<input type="text" class="form-control" name="lastName" value="<?=htmlspecialchars($user['lastName'])?>">
							</div>
							<div class="form-group">
								<label>Email</label>
								<input type="email" class="form-control" name="email" value="<?=htmlspecialchars($user['email'])?>">
							</div>
							<div class="form-group">
								<label>Phone</label>
								<input type="text" class="form-control" name="phone" value="<?=htmlspecialchars($user['phone'])?>">
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-link" data-dismiss="modal">Отмена</button>
							<button type="submit" class="btn btn-primary">Сохранить</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
<?endforeach;?>
<?endif?>
	</div>
</div>

<hr>

<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNewUser">Добавить пользователя</button>

<div class="modal fade" id="addNewUser" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Новый пользователь</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="<?=$baseUrl?>/add_user/" method="post">
			<div class="modal-body">
					<div class="form-group">
						<label>Username</label>
						<input type="text" class="form-control" name="username">
					</div>
					<div class="form-group">
						<label>First name</label>
						<input type="text" class="form-control" name="firstName">
					</div>
					<div class="form-group">
						<label>Last name</label>
						<input type="text" class="form-control" name="lastName">
					</div>
					<div class="form-group">
						<label>Email</label>
						<input type="email" class="form-control" name="email">
					</div>
					<div class="form-group">
						<label>Phone</label>
						<input type="text" class="form-control" name="phone">
					</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link" data-dismiss="modal">Отмена</button>
				<button type="submit" class="btn btn-primary">Сохранить</button>
			</div>
			</form>
		</div>
	</div>
</div>