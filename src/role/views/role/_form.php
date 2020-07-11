<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model mtoto\role\models\Role */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="row">

    <div class="role-form">

        <?php $form = ActiveForm::begin(); ?>

        <div class="col-md-9">
            <?php
            $roles = $model->getAllRoles();
            //        var_dump($roles);exit;
            foreach ($roles as $key => $value): ?>
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><?= $key ?></h6>
                        </div>
                        <div class="panel-body">
                            <?php foreach ($value as $item) {

                                echo Html::checkbox("Items[{$item['name']}]", $item['checked'], ['label' => $item['label']]);
                            } ?>
                        </div>
                    </div>
                </div>
            <?php
            endforeach;
            ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>


            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

        </div>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>


