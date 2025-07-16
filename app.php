<?php

// CRUD = Create Read Update Delete

// Criar uma conexão à base dados
$con = mysqli_connect('127.0.0.1', 'root', '', 'recipe_app');

//verificar se a conexão foi concluida
if ($con) {
    echo "Conexão com a base de dados concluída!\n";
} else {
    echo "Erro na conexão com a base de dados\n";
}



// fechar conexão.
mysqli_close($con);  