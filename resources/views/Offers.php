<?php

$user = $_SESSION['user'];
$user_id = $user['id'];
$offers = $data['offers'] ?? [];

if (!isset($user_id)) {
    header('Location: /');
}

?>

<h3 class="card-title">
    <?php
    echo ucfirst($user['name']) . ", добро пожаловать на страницу предложений!";
    ?>
</h3>
<h6 class="card-subtitle mb-2 text-body-secondary">
    <?php if (isset($user) && $user['role_id'] == '2'): ?>
        Здесь вы можете создать своё предложение.
    <?php else: ?>
        Здесь вы можете переходить по созданным предложениям.
    <?php endif; ?>
</h6>

<!-- dialog create offer -->
<!-- button trigger modal -->
<div class="d-flex w-100 justify-content-between align-items-center">
    <div class="col-9">
        <h5>
            <?php
            if ($user['role_id'] == '2') {
                echo 'Мои предложения';
            } else {
                echo 'Доступные предложения';
            }
            ?>
        </h5>
    </div>
    <div class="col-3 d-flex justify-content-end">
        <?php if (isset($user) && $user['role_id'] == '2'): ?>
            <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                Создать новое
            </button>
        <?php endif; ?>
    </div>
</div>
<!-- /button trigger modal -->

<!-- modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Создать предложение</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- form offer -->
                <form method="post">
                    <input type="hidden" id="form" name="form" value="create_offer">
                    <div class="mb-3">
                        <label for="title" class="col-sm-12 col-form-label">Название</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="title" name="title">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="count" class="col-sm-12 col-form-label">Стоимость перехода</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="count" name="count">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="path" class="col-sm-12 col-form-label">Целевой URL</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="path" name="path">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="theme" class="col-sm-12 col-form-label">Тема</label>
                        <div class="col-sm-12">
                            <select class="form-select" name="theme" id="theme" aria-label="Default select example">
                                <option value="1">theme 1</option>
                                <option value="2">theme 2</option>
                                <option value="3">theme 3</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Создать</button>
                </form>
                <!-- /form offer -->
            </div>
        </div>
    </div>
</div>
<!-- /modal -->
<!-- /dialog create offer -->

<div class="row">
    <?php foreach ($offers as $key => $val): ?>
        <?php
        if (isset($val['id'])):
            ?>
            <div class="col-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            <?= $val['id']; ?>, <?= $val['title']; ?>
                        </h4>
                        <h6 class="card-subtitle mb-2 text-body-secondary">
                            От <?php
                            $date = date_create($val['created']);
                            echo date_format($date, 'd.m.Y');
                            ?>
                        </h6>
                        <p>
                            <?= $val['theme']; ?>
                        </p>

                        <div class="d-flex justify-content-between align-items-center text-danger">
                            <h6 class="mb-0"><?= $val['count'] . ' руб.' ?></h6>

                            <?php if (isset($user) && $user['role_id'] == '3'): ?>
                                <a href="<?= $val['url']; ?>" class="btn btn-primary">Перейти</a>
                            <?php endif; ?>

                            <?php if ($val['user_id'] == $user_id): ?>
                                <?php if ($val['state'] == '1'): ?>
                                    <form method="post">
                                        <input type="hidden" id="form" name="form" value="inactive_offer">
                                        <input name="val_id" id="val_id" type="hidden"
                                               value="<?php echo $val['id']; ?>">
                                        <input name="set_state" id="set_state" type="hidden" value="0">
                                        <button type="submit" class="btn btn-outline-secondary">Деактивировать</button>
                                    </form>

                                <?php elseif ($val['state'] == '0'): ?>
                                    <form method="post">
                                        <input type="hidden" id="form" name="form" value="active_offer">
                                        <input name="val_id" id="val_id" type="hidden"
                                               value="<?php echo $val['id']; ?>">
                                        <input name="set_state" id="set_state" type="hidden" value="1">
                                        <button type="submit" class="btn btn-outline-secondary">Активировать</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <? endif; ?>
    <?php endforeach; ?>
</div>
