<?php

$user = $_SESSION['user'];
$user_id = $user['id'];

if (!isset($user_id)) {
    header('location: /');
}

$offers = $data['offers'];

?>

<div class="card-body">
    <h3 class="card-title">
        <?php
        echo ucfirst($user['name']) . ", добро пожаловать на страницу предложений!";
        ?>
    </h3>
    <h6 class="card-subtitle mb-2 text-body-secondary">Здесь вы можете создать своё предложение.</h6>
</div>

<!-- dialog create offer -->
<!-- button trigger modal -->
<?php if (isset($_SESSION['user']['id']) && $_SESSION['user']['role'] == 'user'): ?>
    <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
        Создать предложение
    </button>
<?php endif; ?>
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
        <?php if ($val['user_id'] == $user_id): ?>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            <?= $val['url']; ?>
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
                            <?php if ($user['role_id'] == 1 || $user['role_id'] == 3): ?>
                                <a href="<?= $val['url']; ?>" class="btn btn-primary">Перейти</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>


            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
