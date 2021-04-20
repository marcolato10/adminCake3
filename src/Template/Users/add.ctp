





<?= $this->Html->css('login') ?>

<div class="container">

<div class="row" style="margin-top:20px">
    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
    	<div class="page-header">
    		<h2>Crear usuario</h2>
    	</div>
        <?= $this->Form->create($user, ['novalidate']) //permite que no funcionen las validaciones html ?>
        <fieldset>
        <?php
            echo $this->Form->input('first_name', ['label' => 'Nombre', 'class' =>'form-control input-lg']);
            echo $this->Form->input('last_name', ['label' => 'Apellidos', 'class' =>'form-control input-lg']);
            echo $this->Form->input('email', ['label' => 'Correo electrónico', 'class' =>'form-control input-lg']);
            echo $this->Form->input('password', ['label' => 'Contraseña', 'value' => '', 'class' =>'form-control input-lg']);
            //echo $this->Form->input('role', ['options' => ['admin' => 'administrador', 'user' => 'usuario'],'class' =>'form-control input-lg']);
            echo $this->Form->input('documento_id', ['label' => 'Documento', 'options' => ['1' => 'email', '2' => 'rut'],'class' =>'form-control input-lg']);
          
        ?>
    
        <hr class="colorgraph">
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6">
                <?= $this->Form->button('Crear', ['class' => 'btn btn-lg btn-success btn-block']) ?>
            </div>
        </div>
        </fieldset>
        <?= $this->Form->end() ?>
    </div>
</div>

</div>