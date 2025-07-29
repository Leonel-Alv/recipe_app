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




function limparterminal() // codigo ascii para limpar o ecra (tipo system cls)
{
    echo "\033[2J";  
}

$fim = false;


//=================================================
//                Menu e seleção
//================================================= 


while (!$fim) 
{
    limparterminal();
    
    echo "Escolha uma opção:\n";
    echo "Criar um nova receita -> 1\n";
    echo "Listar todas as receitas -> 2\n";
    echo "Atualizar receitas existentes -> 3\n";
    echo "Apagar receita -> 4\n";
    echo "Sair do programa -> 0\n";

    //Seleção do menu.
    $menu = readline("");

    switch ($menu) {
        case 0:
            limparterminal();
            echo "Adeus!";
            $fim = true;
            break;

        case 1:
            limparterminal();
            criarreceita($con);
            break;

        case 2:
            limparterminal();
            listarreceitas($con, true);
            break;
        
        case 3:
            limparterminal();
            atualizarreceitas($con, true);
            break;

        case 4:
            limparterminal();
            apagarreceita($con);
            break;

        default:
            echo "Opção inválida!\n";
            break;
    }
}

// ---------------------------------------------------------------------------------------------------------


//=================================================
//               Adicionar receita
//================================================= 

function criarreceita($con)
{
    $nome = readline("Nome da receita: ");
    $preparacao = readline("Descrição da receita: ");
    $tempo_estimado = readline("Tempo de preparação em min: ");
    $num_doses = readline("Doses da receita em gramas: ");

    // Criar comando SQL
    $sql = "INSERT INTO receita (nome, preparacao, tempo_estimado, num_doses) VALUES ('$nome', '$preparacao', $tempo_estimado, $num_doses )";

    //Executar o comando SQL
    if (mysqli_query($con, $sql)) {
        echo "Receita inserida com sucesso\n\n\n";
    } else {
        echo "Erro ao inserir nova receita\n\n\n";
    }
}

// ----------------------------------------------------------------------------------------------------------



//=================================================
//               Listar receitas
//================================================= 

function listarreceitas($con, $voltarMenu){

    // Criar a query
    $sql = "
    SELECT receita.id_receita, receita.nome, receita.preparacao, receita.tempo_estimado, receita.num_doses,
    ingredientes.nome_ingrediente, receita_ingredientes.quantidade, receita_ingredientes.unidade_medida
    FROM receita
    LEFT JOIN receita_ingredientes ON receita.id_receita = receita_ingredientes.id_receita
    LEFT JOIN ingredientes ON receita_ingredientes.id_ingrediente = ingredientes.id_ingrediente
    ORDER BY receita.id_receita
    ";

    //correr a query.
    $resultado = mysqli_query($con, $sql);

    $receita_atual = null; //id da receita que estamos a mostrar 
    $ingredientes = []; // lista dos ingredientes da receita atual

    //fazer um loop pelos resultados da query.
    while ($linha = mysqli_fetch_assoc($resultado))
    {
        $id = $linha["id_receita"];

        if($receita_atual != $id)
            {

            if($receita_atual != null)
            {
                echo " | Ingredientes: " . implode(", ", $ingredientes) . "\n";
            }

            // Começar a criar nova receita
            echo "ID: " . $linha["id_receita"] . " | Nome: " . $linha["nome"] . " | Tempo: " . $linha["tempo_estimado"] . 
            " | Doses: " . $linha ["num_doses"] . "| \n Preparação : " . $linha["preparacao"] ;

            $receita_atual = $id; // atualiza $receita_atual.
            $ingredientes = []; //limpa a lista de ingredientes 
        }


        // mostra ingrediente + quantidade
        $ingredientes[] = $linha["quantidade"] . " " . $linha ["unidade_medida"] . " de " . $linha ["nome_ingrediente"];
    }

    // Imprimir ulitma receita
    if ($receita_atual != null){
        echo " | ingredientes: " . implode(", ", $ingredientes) . "\n";
    }

    if ($voltarMenu){
        voltarMenu();
    }
}

function voltarMenu(){
    $input = "";
    echo "Selecione 0 Para voltar: ";
    while ($input != "0") {
        $input = readline("");
    }
}

// ----------------------------------------------------------------------------------------------------------



//=================================================
//              Atualizar receitas
//================================================= 

function atualizarreceitas($con, $voltarMenu)
{
    listarreceitas($con, false); // mostra as receitas

    $id = readline(" Insere o ID da receita que pretendes atualizar:  ");

    // verifica se o id existe nas receitas
    $sql_verifica = "SELECT * FROM receita WHERE id_receita = $id";
    $verificar = mysqli_query($con, $sql_verifica);
    
    if (mysqli_num_rows($verificar)==0)
    {
        echo "\nO ID escolhido não existe, tente novamente.";

        if($voltarMenu) 
            voltarMenu();
        
    }

    // Atualizar a receita
    $nome = readline("Nome da receita: ");
    $preparacao = readline("Descrição da receita: ");
    $tempo_estimado = readline("Tempo de preparação em min: ");
    $num_doses = readline("Doses da receita em gramas: ");

    $sql_update = "UPDATE receita SET nome = '$nome',preparacao = '$preparacao', 
    tempo_estimado = $tempo_estimado, num_doses = $num_doses WHERE id_receita = $id";

    if(mysqli_query($con, $sql_update)) 
    {
        echo "\nA receita foi atualizada.";
    }
    else
    {   
        echo "Não foi possivel atualizar a receita.\n";
    }

    if ($voltarMenu) voltarMenu();
}

// ----------------------------------------------------------------------------------------------------------


//=================================================
//               Apagar receitas
//================================================= 

function apagarreceita($con)
{
    listarreceitas($con, false); //mostra as receitas e usa false para não voltar ao menu

    $id = readline ("\nInsira o ID da receita que quer apagar: ");

    $sql_verifica = "SELECT * FROM receita WHERE id_receita = $id";
    $resultado = mysqli_query($con, $sql_verifica);

    if (mysqli_num_rows($resultado) == 0)
    { 
        echo "O ID da receita não foi encontrado ";
        voltarMenu();
        return; // para resolver o problema, faz o programa parar caso o id nao exista
    }

    //apaga os ingredientes ligados a receita
    $sql = "DELETE FROM receita_ingredientes WHERE id_receita = $id"; 
    mysqli_query($con, $sql);


    // apaga a receita_categoria
    $sql_delete_categorias = "DELETE FROM receita_categoria WHERE id_receita = $id";
    @mysqli_query($con, $sql_delete_categorias); // ignora erro se ainda não tiveres essa tabela (tirar depois)

    // Apaga a receita
    $sql_delete_receita = "DELETE FROM receita WHERE id_receita = $id";
    if (mysqli_query($con, $sql_delete_receita)) 
    {
        echo "Receita apagada !\n";
    } 

    else 
    {
        echo "Erro ao apagar a receita.\n";
    }

    voltarMenu();

}

// fechar conexão.
mysqli_close($con);  

// A FAZER --> tenho que criar a function de associar os ingredientes as receitas. 
// A FAZER --> preciso de acrescentar ao criar receita a funcionalidade de inserir novos ingredientes 
//             (uso outra function e chamo-a ao criarreceitas) --> (ou faço a parte (?) )
             