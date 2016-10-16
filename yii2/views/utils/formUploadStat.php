<?php
use yii\bootstrap\ActiveForm;
?>
<h1>Загрузка общей статистики рекламных кампаний</h1>
<p>Название кампаний должно быть: "<регион> <категория> <хост> <артикул> все остальное"</p>
<p>Например "rus Фонарь fonar-nalobnik.ru HL720 /РСЯ /телефон,фонарь,одежда,итд"</p>
<p>Категория, хост, артикул должны соответствовать таковым в црм.</p>

<?php $form = ActiveForm::begin(['layout' => 'inline','options' => ['enctype' => 'multipart/form-data'], 'fieldConfig'=>['labelOptions'=>['class'=>'']]])
//$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class'=>'form-inline']]) ?>

    <?= $form->field($model, 'statFile')->fileInput(['class'=>'form-control']) ?>
    
    <?= $form->field($model, 'source')->dropdownList(\app\models\Statcompany::itemAlias('source'), ['prompt'=>'']);?>

    <button type="submit" value="Загрузить файл" name="file-submit" class="btn btn-primary">Загрузить файл</button>

<?php ActiveForm::end() ?>

<p></p>