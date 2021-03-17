// ================================================================================
function adicionarCarrinho(idProduto){
	$.ajax({
		type: 'GET',
		url: `/adiconar_carrinho`,
		data: `id_produto=${idProduto}`,
		dataType: 'json',
		success: dados => {
			if(dados.validacao){
				$('#carrinho').html(dados.total_produtos);
			}else{
				$('#modalText').html('Infelizmente nós só contamos com ' + dados.estoque + ' peças desse modelo atualmente. Mas não se preocupe, temos muitos produtos para você escolhe.')
				//Informa que o limite de peças do produto no estoque
				$('#modalEstoqueInvalido').modal('show');
				if(window.location.href == 'http://localhost:8080/carrinho'){
					subtraiProduto(idProduto);
				}
			}
		},
		erro: erro => {console.log(erro)}
	})
}

// ================================================================================
function closeModal(){
	$('#modalEstoqueInvalido').modal('toggle')
}

// ================================================================================
function removerUnidadeProdutoCarrinho(idProduto){
	$.ajax({
		type: 'GET',
		url: `/remover_unidade_produto_carrinho`,
		data: `id_produto=${idProduto}`,
		dataType: 'json',
		success: dados => {
			$('#carrinho').html(dados)
			if(dados == 0){
				window.location.href = 'http://localhost:8080/carrinho'
			}
		},
		erro: erro => {console.log(erro)}
	})
}

// ================================================================================
function getRemoverProdutoCarrinho(idProduto){
	$.ajax({
		type: 'GET',
		url: `/remover_produto_carrinho`,
		data: `id_produto=${idProduto}`,
		dataType: 'json',
		success: dados => {
			$('#carrinho').html(dados);
			if(dados == 0){
				window.location.href = 'http://localhost:8080/carrinho'
			}
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
	removerUnidadeProdutoCarrinho(idProduto);
	subtraiProduto(idProduto);
}

// ================================================================================
function subtraiProduto(idProduto){
	//Quantidade do mesmo produto no carrinho
	document.getElementById('QuantidaCarrinhoProduto'+idProduto).innerText = parseInt(document.getElementById('QuantidaCarrinhoProduto'+idProduto).innerText) - 1;


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
	
	if(document.getElementById('QuantidaCarrinhoProduto'+idProduto).innerText <= 0){
		document.getElementById('IdTabelaProduto'+idProduto).remove();
	}
}

// ================================================================================
function removerProdutoCarrinho(idProduto){
	getRemoverProdutoCarrinho(idProduto);


	//Valor do produto * a quantidade
	let valorAtualProduto = document.getElementById('ValorQuantidadeCarrinhoProduto'+idProduto).innerText;
	valorAtualProduto = valorAtualProduto.slice(2);
	valorAtualProduto = parseFloat(valorAtualProduto);

	//Valor total do carrinho
	let valorTotalCarrinho = document.getElementById('ValorTotalCarrinho').innerText;
	valorTotalCarrinho = valorTotalCarrinho.slice(2);
	valorTotalCarrinho = parseFloat(valorTotalCarrinho);
	valorTotalCarrinho -= valorAtualProduto;
	document.getElementById('ValorTotalCarrinho').innerText = 'R$' + valorTotalCarrinho.toFixed(2);

	document.getElementById('IdTabelaProduto'+idProduto).remove();

}

// ================================================================================
function showModalLimpar(){
	$('#showModalLimpar').modal('show');
}

// ================================================================================
function closeModalLimpar(){
	$('#showModalLimpar').modal('toggle');
}

// ================================================================================
function enderecoAlternativo(){
	//Mostrar ou escontder o form de endereço alternativo
	// enderecoAlternativo
	let form = document.getElementById('enderecoAlternativo');
	if(form.checked == true){
		//mostra o from
		$('#campoEnderecoAlternativo').css({
			"display" : "inline"
		})
	}else {
		//esconde o form
		$('#campoEnderecoAlternativo').css({
			"display" : "none"
		})
	}
}

// ================================================================================
function definirDadosAlternativos(){
	let form = document.getElementById('enderecoAlternativo');
	if(form.checked == true){
		let cidadeAlternativa = $('#Cidade').val();
		let enderecoAlternativa = $('#rua').val() + ' ' + $('#numero_casa').val();
		let cepAlternativa = $('#cep').val();
		$.post(
			'/adicionar_dados_alternativos',
			{
				cepAlternativa: cepAlternativa,
				enderecoAlternativa: enderecoAlternativa,
				cidadeAlternativa: cidadeAlternativa
			},
			function(data){
				
		})
	} else{
		$.post(
			'/remove_dados_alternativos',
			{
				remover: true
			},
			function(data){}
		)
	}
}