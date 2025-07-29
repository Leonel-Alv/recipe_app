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
    echo "Criar um nova categoria -> 5\n";
    echo "Listar todas as categorias -> 6\n";
    echo "Associar uma receita a uma categoria -> 7\n";
    echo "Desassociar uma receita a uma categoria -> 8\n";
    echo "Consultar receitas filtradas por categoria -> 9\n";
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

        case 5:
            limparterminal();
            criarcategoria($con);
            break;

        case 6:
            limparterminal();
            listarcategoria($con, true);
            break;

        case 7:
            limparterminal();
            associarreceitacategoria($con, true);
            break;

        case 8:
            limparterminal();
            desassociarreceitacategoria($con, true);
            break;

        case 9:
            limparterminal();
            receitafiltradacategoria($con, true);
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

    if ($voltarMenu)
    {
        voltarMenu();
    }
}

function voltarMenu(){
    $input = "";
    echo "Selecione 0 Para voltar: ";
    while ($input != "0") 
    {
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

    if ($voltarMenu)
    {
        voltarMenu();
    }
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



// ----------------------------------------------------------------------------------------------------------




//=================================================
//               Criar Categoria
//================================================= 



function criarcategoria($con)
{
    $nome = readline("Nome da categoria: ");

    // Criar comando SQL
    $sql = "INSERT INTO categoria (tipo_categoria) VALUES ('$nome')";

    //Executar o comando SQL
    if (mysqli_query($con, $sql)) {
        echo "Categoria inserida com sucesso\n\n\n"; 
    } 

    else 
    {
        echo "Erro ao inserir nova categoria\n\n\n";
    }

    if ($voltarMenu) 
    {
        voltarMenu();
    }
}




// ----------------------------------------------------------------------------------------------------------





//=================================================
//               Listar Categoria
//================================================= 



function listarcategoria($con, $voltarMenu)

{
    $sql = "SELECT id_categoria, tipo_categoria FROM categoria ORDER BY id_categoria";


    $resultado = mysqli_query($con, $sql);

    echo "\n--- Lista de Categorias ---\n";

    while ($linha = mysqli_fetch_assoc($resultado)) 

    {
        echo "ID: " . $linha["id_categoria"] . " | Nome: " . $linha["tipo_categoria"] . "\n"; 
    }


    if ($voltarMenu) 
    {
        voltarMenu();
    }

}




// ----------------------------------------------------------------------------------------------------------



//=================================================
//        Associar Receita a Categoria
//=================================================


function associarreceitacategoria($con)
{
    echo "\nAssociar uma receita a uma categoria \n";

    // Mostrar as receitas
    $sql_receitas = "SELECT id_receita, nome FROM receita ORDER BY id_receita";
    $res_receitas = mysqli_query($con, $sql_receitas);

    echo "\n--- Receitas Disponíveis ---\n";
    while ($linha = mysqli_fetch_assoc($res_receitas)) 
    {
        echo "ID: " . $linha["id_receita"] . " | Nome: " . $linha["nome"] . "\n";
    }

    // Mostra as categorias
    $sql_categorias = "SELECT id_categoria, tipo_categoria FROM categoria ORDER BY id_categoria";
    $res_categorias = mysqli_query($con, $sql_categorias);

    echo "\n Categorias : \n";
    while ($linha = mysqli_fetch_assoc($res_categorias)) 
    {
        echo "ID: " . $linha["id_categoria"] . " | Tipo: " . $linha["tipo_categoria"] . "\n";
    }

    // pede ids
    $id_receita = readline("\nID da receita que quer associar: ");
    $id_categoria = readline("ID da categoria que quer associar: ");

    // Verificar se o ID da receita existe
    $verificareceita = mysqli_query($con, "SELECT id_receita FROM receita WHERE id_receita = $id_receita");
    if (mysqli_num_rows($verificareceita) == 0) 
    {
        echo "\nErro: Receita com ID $id_receita não existe.\n";
        voltarMenu();
        return;
    }

    // Verificar se o id da categoria existe
    $verificacategoria = mysqli_query($con, "SELECT id_categoria FROM categoria WHERE id_categoria = $id_categoria");
    if (mysqli_num_rows($verificacategoria) == 0) 
    {
        echo "\nErro: Categoria com ID $id_categoria não existe.\n";
        voltarMenu();
        return;
    }

    // Verifica e associa
    $sql = "INSERT INTO receita_categoria (id_receita, id_categoria) VALUES ($id_receita, $id_categoria)";

    if (mysqli_query($con, $sql)) 
    {
        echo "\nReceita associada com sucesso\n";
    } 
    
    else 
    {
        echo "\nErro ao associar receita a categoria.\n";
    }

    voltarMenu();
}



// ----------------------------------------------------------------------------------------------------------



//=================================================
//     Desassociar Receita de Categoria 
//=================================================


function desassociarReceitaCategoria($con)
{
    echo "\nDesassociar Receita de Categoria : \n";

    
    $sql = "
    SELECT receita_categoria.id_receita_categoria, 
    receita.nome AS nome_receita, 
    categoria.tipo_categoria AS nome_categoria
    FROM receita_categoria
    JOIN receita ON receita_categoria.id_receita = receita.id_receita
    JOIN categoria ON receita_categoria.id_categoria = categoria.id_categoria
    ORDER BY receita_categoria.id_receita_categoria
    ";

    $res = mysqli_query($con, $sql);

    if (mysqli_num_rows($res) == 0) {
        echo "Não existem associações feitas.\n";
        voltarMenu();
        return;
    }

    echo "\nAssociações :\n";
    while ($linha = mysqli_fetch_assoc($res)) 
    {
        echo "ID Associação: " . $linha["id_receita_categoria"] . 
             " | Receita: " . $linha["nome_receita"] . 
             " | Categoria: " . $linha["nome_categoria"] . "\n";
    }

    // Escolher a associação a remover
    $id_associacao = readline("\nInsere o ID da associação que queres desassociar: ");

    // Verificar se existe
    $verifica = mysqli_query($con, "SELECT * FROM receita_categoria WHERE id_receita_categoria = $id_associacao");

    if (mysqli_num_rows($verifica) == 0) 
    {
        echo "Id da associação invalido.\n";
        voltarMenu();
        return;
    }

    // Remover receita da categoria
    $sql_delete = "DELETE FROM receita_categoria WHERE id_receita_categoria = $id_associacao";

    if (mysqli_query($con, $sql_delete)) 
    {
        echo "Desassociação terminada com sucesso.\n";
    } 

    else 
    {
        echo "Erro ao desassociar categoria.\n";
    }

    voltarMenu();
}





// ----------------------------------------------------------------------------------------------------------



//=================================================
//       Filtrar receita por categoria
//=================================================


function receitafiltradacategoria($con)
{
    echo "\nCategorias Disponíveis : \n";

    // Mostra categorias
    $sql_categorias = "SELECT id_categoria, tipo_categoria FROM categoria ORDER BY id_categoria";
    $res_categorias = mysqli_query($con, $sql_categorias);

    while ($linha = mysqli_fetch_assoc($res_categorias)) {
        echo "ID: " . $linha["id_categoria"] . " | Nome: " . $linha["tipo_categoria"] . "\n";
    }

    // Pede o id 
    $id_categoria = readline("\nInsere o ID da categoria que queres consultar: ");

    // Vê se a categoria esta registada
    $verifica = mysqli_query($con, "SELECT * FROM categoria WHERE id_categoria = $id_categoria");
    if (mysqli_num_rows($verifica) == 0) {
        echo "Essa categoria não existe.\n";
        voltarMenu();
        return;
    }

    $sql = "
    SELECT r.id_receita, r.nome, r.preparacao, r.tempo_estimado, r.num_doses
    FROM receita_categoria rc
    JOIN receita r ON rc.id_receita = r.id_receita
    WHERE rc.id_categoria = $id_categoria
    ORDER BY r.id_receita
    ";

    $resultado = mysqli_query($con, $sql);

    echo "\nReceitas associadas : \n";

    if (mysqli_num_rows($resultado) == 0) 
    {
        echo "Para esta categoria não existe nenhuma receita associada\n";
    } 

    else 
    {
        while ($linha = mysqli_fetch_assoc($resultado)) 
        {
            echo "ID: " . $linha["id_receita"] . " | Nome: " . $linha["nome"] ." | Tempo: " . $linha["tempo_estimado"] . " min 
            | Doses: " . $linha["num_doses"] ."\nPreparação: " . $linha["preparacao"] . "\n\n";
        }
    }

    voltarMenu();
}



// fechar conexão.
mysqli_close($con);  


             