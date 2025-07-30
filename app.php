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
    echo "Listar receitas filtradas por categoria -> 9\n";
    echo "Adicionar novo ingrediente -> 10\n";
    echo "Listar ingredientes -> 11\n";
    echo "Associar um ingrediente a uma receita -> 12\n";
    echo "Atualizar a quantidade/unidade dos ingredientes -> 13\n";
    echo "Remover um ingrediente de uma receita -> 14\n";
    echo "Ver detalhes de uma receita -> 15\n";
    echo "Listar receitas de uma categoria -> 16\n";
    echo "Listar receitas com um determinado ingrediente-> 17\n";
    echo "Pesquisar nome da receita-> 18\n";
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
            criarreceita($con,true);
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
            criarcategoria($con,true);
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

        case 10:
            limparterminal();
            adicionaringrediente($con, true);
            break;

        case 11:
            limparterminal();
            listaringrediente($con, true);
            break;

        case 12:
        limparterminal();
        ingredientereceita($con);
        break;

        case 13:
        limparterminal();
        atualizarunidadequantidade($con,true);
        break;

        case 14:
        limparterminal();
        removeringredientedereceita($con);
        break;

        case 15:
        limparterminal();
        detalhereceita($con);
        break;

        case 16:
        limparterminal();
        receitacategoria($con);
        break;

        case 17:
        limparterminal();
        pesquisaingri($con);
        break;

        case 18:
        limparterminal();
        nomereceita($con);
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

function criarreceita($con,$voltarMenu)
{
    $nome = readline("Nome da receita: ");
    $preparacao = readline("Preparação da receita: ");
    $tempo_estimado = readline("Tempo de preparação em min: ");
    $num_doses = readline("Numero de doses: ");

    // Criar comando SQL
    $sql = "INSERT INTO receita (nome, preparacao, tempo_estimado, num_doses) VALUES ('$nome', '$preparacao', $tempo_estimado, $num_doses )";

    //Executar o comando SQL
    if (mysqli_query($con, $sql)) {
        echo "\nReceita inserida com sucesso\n\n\n";
    } else {
        echo "\nErro ao inserir nova receita\n\n\n";
    }

    if ($voltarMenu) 
    {
        voltarMenu();
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

    
    $resultado = mysqli_query($con, $sql);

    $receita_atual = null;  
    $ingredientes = []; 

    //faz um loop
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
            echo "\n" . "ID: " . $linha["id_receita"] . " | Nome: " . $linha["nome"] . " | Tempo: " . $linha["tempo_estimado"] . " | Doses: " . $linha ["num_doses"] .
             "| \n Preparação : " . $linha["preparacao"] ;

            $receita_atual = $id; // atualiza as receitas
            $ingredientes = []; //limpa a lista
        }


        // mostra ingrediente + quantidade
        $ingredientes[] = $linha["quantidade"] . " " . $linha ["unidade_medida"] . " de " . $linha ["nome_ingrediente"];
    }

    
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

    $id = readline("\nInsere o ID da receita que pretendes atualizar:  ");

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



function criarcategoria($con,$voltarMenu)
{
    $nome = readline("Nome da categoria: ");

    
    $sql = "INSERT INTO categoria (tipo_categoria) VALUES ('$nome')";

    
    if (mysqli_query($con, $sql)) 
    {
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
        echo "\nA receita com esse ID não existe.\n";
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






// ----------------------------------------------------------------------------------------------------------



//=================================================
//            Adicionar ingrediente
//=================================================


function adicionaringrediente($con)
{
    $nome = readline("Nome do ingrediente: ");

    $sql = "INSERT INTO ingredientes (nome_ingrediente) VALUES ('$nome')";

    if (mysqli_query($con, $sql))
    {
        echo "O ingrediente foi adicionado\n";
    } 
    
    else 
    {
        echo "Erro ao adicionar ingrediente.\n";
    }

    voltarMenu();
}




// ----------------------------------------------------------------------------------------------------------



//=================================================
//            Listar ingredientes
//=================================================


function listaringrediente($con)
{
    $sql = "
        SELECT i.id_ingrediente, i.nome_ingrediente, r.nome AS nome_receita,ri.quantidade, ri.unidade_medida
        FROM ingredientes i
        LEFT JOIN receita_ingredientes ri ON i.id_ingrediente = ri.id_ingrediente
        LEFT JOIN receita r ON ri.id_receita = r.id_receita
        ORDER BY i.id_ingrediente
    ";
    $res = mysqli_query($con, $sql);

    echo "\nLista de Ingredientes\n";

    while ($linha = mysqli_fetch_assoc($res)) 
    {
        echo "ID: " . $linha["id_ingrediente"] . " | Nome: " . $linha["nome_ingrediente"] . " | Quantidade: " . $linha["quantidade"] .
    " | " . $linha["unidade_medida"] ."\n";
    }

    voltarMenu();
}




// ----------------------------------------------------------------------------------------------------------



//=================================================
//        Associar ingrediente a receita
//=================================================


function ingredientereceita($con)
{
    echo "\nAssociar Ingrediente a Receita\n";

    // Mostra as receitas
    $sql_receitas = "SELECT id_receita, nome FROM receita ORDER BY id_receita";
    $res_receitas = mysqli_query($con, $sql_receitas);

    echo "\nReceitas disponíveis:\n";
    while ($linha = mysqli_fetch_assoc($res_receitas)) 
    {
        echo "ID: " . $linha["id_receita"] . " | Nome: " . $linha["nome"] . "\n";
    }

    // Mostra os ingredientes
    $sql_ingredientes = "SELECT id_ingrediente, nome_ingrediente FROM ingredientes ORDER BY id_ingrediente";
    $res_ingredientes = mysqli_query($con, $sql_ingredientes);

    echo "\nIngredientes disponíveis:\n";
    while ($linha = mysqli_fetch_assoc($res_ingredientes)) 
    {
        echo "ID: " . $linha["id_ingrediente"] . " | Nome: " . $linha["nome_ingrediente"] . "\n";
    }

    
    $id_receita = readline("\nID da receita: ");
    $id_ingrediente = readline("ID do ingrediente: ");
    $quantidade = readline("Quantidade: ");
    $unidade = readline("Unidade de medida (gramas,litros etc): ");

    // Vê se essa receita e ingrediente existe
    $verificaReceita = mysqli_query($con, "SELECT * FROM receita WHERE id_receita = $id_receita");
    $verificaIngrediente = mysqli_query($con, "SELECT * FROM ingredientes WHERE id_ingrediente = $id_ingrediente");

    if (mysqli_num_rows($verificaReceita) == 0 || mysqli_num_rows($verificaIngrediente) == 0) 
    {
        echo "ID de receita ou ingrediente inválido.\n";
        voltarMenu();
        return;
    }


    $sql_insert = "INSERT INTO receita_ingredientes (id_receita, id_ingrediente, quantidade, unidade_medida)
                   VALUES ($id_receita, $id_ingrediente, '$quantidade', '$unidade')";

    if (mysqli_query($con, $sql_insert)) 
    {
        echo "\nIngrediente associado com sucesso!\n";
    } 
    
    else 
    {
        echo "\nErro ao associar ingrediente.\n";
    }

    voltarMenu();
}




// ----------------------------------------------------------------------------------------------------------



//================================================================
//  Atualizar quantidade/unidade de ingredientes de uma receita 
//================================================================


function atualizarunidadequantidade($con, $voltarMenu)
{
    echo "\nAtualizar Ingrediente de Receita\n";

    
    $sql_receitas = "SELECT id_receita, nome FROM receita ORDER BY id_receita";
    $res_receitas = mysqli_query($con, $sql_receitas);

    echo "\nReceitas disponíveis:\n";
    while ($linha = mysqli_fetch_assoc($res_receitas)) 
    {
        echo "ID: " . $linha["id_receita"] . " | Nome: " . $linha["nome"] . "\n";
    }

    $id_receita = readline("\nID da receita: ");

    // Verificar se receita existe
    $verificaReceita = mysqli_query($con, "SELECT * FROM receita WHERE id_receita = $id_receita");
    if (mysqli_num_rows($verificaReceita) == 0) 
    {
        echo "\nNão existe nenhuma receita com esse id\n";
        voltarMenu();
        return;
    }

    // Mostra os ingredientes das receitas
    $sql_ingredientes = "
        SELECT ri.id_ingrediente, i.nome_ingrediente, ri.quantidade, ri.unidade_medida
        FROM receita_ingredientes ri
        JOIN ingredientes i ON ri.id_ingrediente = i.id_ingrediente
        WHERE ri.id_receita = $id_receita
    ";
    $res_ingredientes = mysqli_query($con, $sql_ingredientes);

    echo "\nIngredientes da receita:\n";

    while ($linha = mysqli_fetch_assoc($res_ingredientes)) 
    {
        echo "ID Ingrediente: " . $linha["id_ingrediente"] . " | Nome: " . $linha["nome_ingrediente"] . " | Quantidade: " . $linha["quantidade"] . 
        " " . $linha["unidade_medida"] . "\n";
    }

    $id_ingrediente = readline("\nID do ingrediente a atualizar: ");

    
    $verifica = mysqli_query($con, "SELECT * FROM receita_ingredientes WHERE id_receita = $id_receita AND id_ingrediente = $id_ingrediente");
    
    if (mysqli_num_rows($verifica) == 0) 
    {
        echo "\nEsse ingrediente não esta associado a esta receita.\n";
        voltarMenu();
        return;
    }

    $quantidade = readline("Nova quantidade: ");
    $unidade = readline("Nova unidade de medida: ");

    $sql_update = "
        UPDATE receita_ingredientes 
        SET quantidade = '$quantidade', unidade_medida = '$unidade' 
        WHERE id_receita = $id_receita AND id_ingrediente = $id_ingrediente
    ";

    if (mysqli_query($con, $sql_update)) 
    {
        echo "\nIngrediente atualizado com sucesso!\n";
    } 
    
    else 
    {
        echo "\nErro ao atualizar ingrediente.\n";
    }

    if ($voltarMenu) 
    {
        voltarMenu();
    }
}



// ----------------------------------------------------------------------------------------------------------



//================================================================
//                      Remover ingredientes
//================================================================


function removeringredientedereceita($con)
{
    echo "\nRemover Ingrediente de Receita\n";

   
    $sql_receitas = "SELECT id_receita, nome FROM receita ORDER BY id_receita";
    $res_receitas = mysqli_query($con, $sql_receitas);

    echo "\nReceitas disponíveis:\n";
    while ($linha = mysqli_fetch_assoc($res_receitas)) 
    {
        echo "ID: " . $linha["id_receita"] . " | Nome: " . $linha["nome"] . "\n";
    }

    $id_receita = readline("\nID da receita: ");

    $verifica = mysqli_query($con, "SELECT * FROM receita WHERE id_receita = $id_receita");
    if (mysqli_num_rows($verifica) == 0) 
    {
        echo "Receita não existe.\n";
        voltarMenu();
        return;
    }

    // Mostra ingredientes
    $sql_ingredientes = "
        SELECT i.id_ingrediente, i.nome_ingrediente, ri.quantidade, ri.unidade_medida
        FROM receita_ingredientes ri
        JOIN ingredientes i ON ri.id_ingrediente = i.id_ingrediente
        WHERE ri.id_receita = $id_receita
    ";
    $res_ingredientes = mysqli_query($con, $sql_ingredientes);

    echo "\nIngredientes associados à receita:\n";
    while ($linha = mysqli_fetch_assoc($res_ingredientes)) 
    {
        echo "ID: " . $linha["id_ingrediente"] . " | Nome: " . $linha["nome_ingrediente"] ." | Quantidade: " . $linha["quantidade"] . " " . $linha["unidade_medida"] . "\n";
    }

    $id_ingrediente = readline("\nID do ingrediente a remover: ");

    
    $verifica_assoc = mysqli_query($con, "
        SELECT * FROM receita_ingredientes 
        WHERE id_receita = $id_receita AND id_ingrediente = $id_ingrediente
    ");

    if (mysqli_num_rows($verifica_assoc) == 0) 
    {
        echo "Essa associação não existe.\n";
        voltarMenu();
        return;
    }

    // Apaga a associação
    $sql_delete = "
        DELETE FROM receita_ingredientes 
        WHERE id_receita = $id_receita AND id_ingrediente = $id_ingrediente
    ";

    if (mysqli_query($con, $sql_delete)) 
    {
        echo "Ingrediente removido da receita com sucesso.\n";
    } 
    
    else 
    {
        echo "Erro ao remover ingrediente.\n";
    }

    voltarMenu();
}






// ----------------------------------------------------------------------------------------------------------



//================================================================
//                      Receitas Detalhadas
//================================================================



function detalhereceita($con)
{
    echo "\nReceitas disponíveis\n";

    // Mostra lista de receitas
    $sql_receitas = "SELECT id_receita, nome FROM receita ORDER BY id_receita";
    $res_receitas = mysqli_query($con, $sql_receitas);

    while ($linha = mysqli_fetch_assoc($res_receitas)) 
    {
        echo "ID: " . $linha["id_receita"] . " | Nome: " . $linha["nome"] . "\n";
    }

    // 
    $id_receita = readline("\nInsere o ID da receita que queres ver em detalhe: ");

    // Verifica
    $verifica = mysqli_query($con, "SELECT * FROM receita WHERE id_receita = $id_receita");
    if (mysqli_num_rows($verifica) == 0) 
    {
        echo "\nEssa receita não existe.\n";
        voltarMenu();
        return;
    }

    // Dados da receita
    $sql_detalhes = "SELECT * FROM receita WHERE id_receita = $id_receita";
    $res_detalhes = mysqli_query($con, $sql_detalhes);
    $detalhe = mysqli_fetch_assoc($res_detalhes);

   echo "\n--- Detalhes da Receita ---\n" ."Nome: " . $detalhe["nome"] . "\n" ."Tempo: " . $detalhe["tempo_estimado"] . " minutos\n" ."Doses: " . 
   $detalhe["num_doses"] . "\n" ."Preparação: " . $detalhe["preparacao"] . "\n";

    // Ingredientes
    echo "\nIngredientes:\n";
    $sql_ingredientes = "
        SELECT i.nome_ingrediente, ri.quantidade, ri.unidade_medida
        FROM receita_ingredientes ri
        JOIN ingredientes i ON ri.id_ingrediente = i.id_ingrediente
        WHERE ri.id_receita = $id_receita
    ";
    $res_ingredientes = mysqli_query($con, $sql_ingredientes);


    if (mysqli_num_rows($res_ingredientes) == 0) 
    {
        echo "- Sem ingredientes associados.\n";
    } 
    
    else 
    {
        while ($linha = mysqli_fetch_assoc($res_ingredientes)) 
        {
            echo "- " . $linha["quantidade"] . " " . $linha["unidade_medida"] . " de " . $linha["nome_ingrediente"] . "\n";
        }
    }

    // Categorias
    echo "\nCategorias:\n";
    $sql_categorias = "
        SELECT c.tipo_categoria 
        FROM receita_categoria rc
        JOIN categoria c ON rc.id_categoria = c.id_categoria
        WHERE rc.id_receita = $id_receita
    ";
    $res_categorias = mysqli_query($con, $sql_categorias);

    if (mysqli_num_rows($res_categorias) == 0) 
    {
        echo "- Sem categoria associada.\n";
    } 
    
    else 
    {
        while ($linha = mysqli_fetch_assoc($res_categorias)) 
        {
            echo "- " . $linha["tipo_categoria"] . "\n";
        }
    }

    voltarMenu();
}



// ----------------------------------------------------------------------------------------------------------



//================================================================
//                      Receitas Detalhadas
//================================================================


function receitacategoria($con)
{
    echo "\nPesquisa de Receitas por Categoria\n";

    // Mostra as categorias
    $sql_cat = "SELECT id_categoria, tipo_categoria FROM categoria ORDER BY id_categoria";
    $res_cat = mysqli_query($con, $sql_cat);

    while ($linha = mysqli_fetch_assoc($res_cat)) 
    {
        echo "ID: " . $linha["id_categoria"] . " | Nome: " . $linha["tipo_categoria"] . "\n";
    }

    // Pesquisa por ID/Nome
    $input = readline("\nInsere o ID ou Nome da categoria: ");


    if (is_numeric($input)) 
    {
        $sql = "
            SELECT r.id_receita, r.nome, r.preparacao, r.tempo_estimado, r.num_doses
            FROM receita_categoria rc
            JOIN receita r ON rc.id_receita = r.id_receita
            WHERE rc.id_categoria = $input
        ";
    } 
    
    else 
    {
        $input = mysqli_real_escape_string($con, $input);
        $sql = "
            SELECT r.id_receita, r.nome, r.preparacao, r.tempo_estimado, r.num_doses
            FROM receita_categoria rc
            JOIN receita r ON rc.id_receita = r.id_receita
            JOIN categoria c ON rc.id_categoria = c.id_categoria
            WHERE LOWER(c.tipo_categoria) = LOWER('$input')
        ";
    }

    $res = mysqli_query($con, $sql);

    echo "\nReceitas associadas:\n";

    if (mysqli_num_rows($res) == 0) 
    {
        echo "Nenhuma receita encontrada para essa categoria.\n";
    } 
    
    else 
    
    {
        while ($linha = mysqli_fetch_assoc($res)) 
        {
        echo "ID: " . $linha["id_receita"] . " | Nome: " . $linha["nome"] . " | Tempo: " . $linha["tempo_estimado"] . " min | Doses: " . $linha["num_doses"] . 
        "\nPreparação: " . $linha["preparacao"] . "\n\n";
        }
    }

    voltarMenu();
}



// ----------------------------------------------------------------------------------------------------------



//================================================================
//              pesquisa categoria por ingrediente
//================================================================


function pesquisaingri($con)
{
    echo "\nPesquisar Receitas por Ingrediente\n";

    // Mostra ingredientes
    $sql_ingredientes = "SELECT id_ingrediente, nome_ingrediente FROM ingredientes ORDER BY id_ingrediente";
    $res_ingredientes = mysqli_query($con, $sql_ingredientes);

    echo "\nIngredientes disponíveis:\n";
    while ($linha = mysqli_fetch_assoc($res_ingredientes)) 
    {
        echo "ID: " . $linha["id_ingrediente"] . " | Nome: " . $linha["nome_ingrediente"] . "\n";
    }

    //pede id
    $id_ingrediente = readline("\nInsere o ID do ingrediente a procurar: ");
    $id_ingrediente = (int) $id_ingrediente;


    $verifica = mysqli_query($con, "SELECT * FROM ingredientes WHERE id_ingrediente = $id_ingrediente");
    if (mysqli_num_rows($verifica) == 0) 
    {
        echo "Esse ingrediente não existe.\n";
        voltarMenu();
        return;
    }

    
    $sql = "
        SELECT DISTINCT r.id_receita, r.nome, r.preparacao, r.tempo_estimado, r.num_doses
        FROM receita r
        JOIN receita_ingredientes ri ON r.id_receita = ri.id_receita
        WHERE ri.id_ingrediente = $id_ingrediente
        ORDER BY r.id_receita
    ";

    $res = mysqli_query($con, $sql);

    echo "\nReceitas que utilizam o ingrediente com ID $id_ingrediente:\n";

    if (mysqli_num_rows($res) == 0) 
    {
        echo "Nenhuma receita encontrada com esse ingrediente.\n";
    } 
    
    else 
    {
        while ($linha = mysqli_fetch_assoc($res)) 
        {
            echo "ID: " . $linha["id_receita"] . " | Nome: " . $linha["nome"] ." | Tempo: " . $linha["tempo_estimado"] . " min | Doses: " . $linha["num_doses"] . 
            "\nPreparação: " . $linha["preparacao"] . "\n\n";
        }
    }

    voltarMenu();
}



//Ver detalhes completos de uma receita 
//○ Dado um ID ou nome da receita, apresentar: 
//■ Título 
//■ Etapas de preparação (descrição) 
//■ Ingredientes, quantidades e unidades
// já implementado em listar receitas




// ----------------------------------------------------------------------------------------------------------



//================================================================
//                 pesquisa receita pelo nome
//================================================================


function nomereceita($con)
{
    echo "\nPesquisar receita pelo nome\n";

    $procura = readline("Insere o nome ou parte do nome da receita a procurar: ");

    $sql = "
        SELECT id_receita, nome, preparacao, tempo_estimado, num_doses
        FROM receita
        WHERE nome LIKE '%$procura%'
        ORDER BY id_receita
    ";

    $res = mysqli_query($con, $sql);

    echo "\nResultados da pesquisa por '$procura':\n";

    if (mysqli_num_rows($res) == 0) 
    {
        echo "Nenhuma receita encontrada com esse termo.\n";
    } 
    
    else 
    {
        while ($linha = mysqli_fetch_assoc($res)) 
        {
            echo "ID: " . $linha["id_receita"] . " | Nome: " . $linha["nome"] .  " | Tempo: " . $linha["tempo_estimado"] . " min | Doses: " . $linha["num_doses"] . 
            "\nPreparação: " . $linha["preparacao"] . "\n\n";
        }
    }

    voltarMenu();
}

// fechar conexão.
mysqli_close($con);  


             