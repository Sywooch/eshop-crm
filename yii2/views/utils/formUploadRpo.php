<?php
use yii\bootstrap\ActiveForm;
use app\models\Senders;
?>
<h1>Загрузка почтовых отправлений</h1>
<?php $form = ActiveForm::begin(['layout' => 'inline','options' => ['enctype' => 'multipart/form-data'], 'fieldConfig'=>['labelOptions'=>['class'=>'']]])
//$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class'=>'form-inline']]) ?>

    <?= $form->field($model, 'sender')->dropdownList(Senders::find()->select(['name', 'id'])->indexBy('id')->column(), ['prompt'=>'']);?>
    
    <?= $form->field($model, 'statFile')->fileInput(['class'=>'form-control']) ?>

    <button type="submit" value="Загрузить файл" name="file-submit" class="btn btn-primary">Загрузить файл</button>

<?php ActiveForm::end() ?>

<? if(!empty($errors)) {
	print_r($errors);
}?>

<?if ($count>0 or $num>0) :?>
<h2>Добавлено:</h2>
<p>Обработано строк: <?=$num?></p>
<p>Изменено заказов: <?=$count?></p>
<? endif; ?>