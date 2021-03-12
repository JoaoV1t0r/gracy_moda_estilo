// ================================================================================
function adicionarCarrinho(idProduto){
	$.ajax({
		type: 'GET',
		url: `/adiconar_carrinho`,
		data: `id_produto=${idProduto}`,
		dataType: 'json',
		success: dados => {
			$('#carrinho').html(dados)
		},
		erro: erro => {console.log(erro)}
	})
}

// ================================================================================
function removerProdutoCarrinho(idProduto){
	$.ajax({
		type: 'GET',
		url: `/remover_produto_carrinho`,
		data: `id_produto=${idProduto}`,
		dataType: 'json',
		success: dados => {
			$('#carrinho').html(dados)
		},
		erro: erro => {console.log(erro)}
	})
}

// ================================================================================
function maisUmProduto(idProduto){
	adicionarCarrinho(idProduto);

	//Quantidade do mesmo produto no carrinho
	document.getElementById('QuantidaCarrinhoProduto'+idProduto).innerText = parseInt(document.getElementById('QuantidaCarrinhoProduto'+idProduto).innerText) + 1;

	//Valor do produto adicionado
	valorProduto = document.getElementById('ValorProduto'+idProduto).innerText;
	valorProduto = valorProduto.slice(2);
	valorProduto = parseFloat(valorProduto);
	
	//Valor do produto * a quantidade
	valorAtualProduto = document.getElementById('ValorQuantidadeCarrinhoProduto'+idProduto).innerText;
	valorAtualProduto = valorAtualProduto.slice(2);
	valorAtualProduto = parseFloat(valorAtualProduto);
	valorAtualProduto += valorProduto
	document.getElementById('ValorQuantidadeCarrinhoProduto'+idProduto).innerText = 'R$' + valorAtualProduto + ',00';

	//Valor total do carrinho
	valorTotalCarrinho = document.getElementById('ValorTotalCarrinho').innerText;
	valorTotalCarrinho = valorTotalCarrinho.slice(2);
	valorTotalCarrinho = parseFloat(valorTotalCarrinho);
	valorTotalCarrinho += valorProduto
	document.getElementById('ValorTotalCarrinho').innerText = 'R$' + valorTotalCarrinho + ',00'
}

// ================================================================================
function menosUmProduto(idProduto){
	removerProdutoCarrinho(idProduto);

	//Quantidade do mesmo produto no carrinho
	document.getElementById('QuantidaCarrinhoProduto'+idProduto).innerText = parseInt(document.getElementById('QuantidaCarrinhoProduto'+idProduto).innerText) - 1;

	if(document.getElementById('QuantidaCarrinhoProduto'+idProduto).innerText <= 0){
		window.location.href = "http://localhost:8080/carrinho";
	}

	//Valor do produto adicionado
	let valorProduto = document.getElementById('ValorProduto'+idProduto).innerText;
	valorProduto = valorProduto.slice(2);
	valorProduto = parseFloat(valorProduto);
	
	//Valor do produto * a quantidade
	let valorAtualProduto = document.getElementById('ValorQuantidadeCarrinhoProduto'+idProduto).innerText;
	valorAtualProduto = valorAtualProduto.slice(2);
	valorAtualProduto = parseFloat(valorAtualProduto);
	valorAtualProduto -= valorProduto;
	document.getElementById('ValorQuantidadeCarrinhoProduto'+idProduto).innerText = 'R$' + valorAtualProduto .toFixed(2);

	//Valor total do carrinho
	let valorTotalCarrinho = document.getElementById('ValorTotalCarrinho').innerText;
	valorTotalCarrinho = valorTotalCarrinho.slice(2);
	valorTotalCarrinho = parseFloat(valorTotalCarrinho);
	valorTotalCarrinho -= valorProduto;
	document.getElementById('ValorTotalCarrinho').innerText = 'R$' + valorTotalCarrinho.toFixed(2);
}