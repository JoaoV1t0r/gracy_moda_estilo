// ================================================================================
$('#CEP').on('blur' , e => {
	let cep = $(e.target).val().replace(/[-]+/g, '')
	document.getElementById('CEP').value = cep.substring(0,5)+"-"+cep.substring(5);
	if(!isNaN(cep) && cep.length == 8){
		$.ajax({
			type: 'GET',
			url: 'https://viacep.com.br/ws/'+ cep +'/json/unicode/',
			dataType: 'json',
			success: dados => {
				if(!("erro" in dados)){
					document.getElementById('Rua').value = dados.logradouro
					document.getElementById('Bairro').value = dados.bairro
					document.getElementById('Cidade').value = dados.localidade
				}else{
					alert("CEP não encontrado, tente novamente")
				}
			},
			erro: erro => {console.log(erro)}
		})
	} else {
		alert("CEP inválido")
	}
})

// ================================================================================
$('#NumeroCasa').on('blur', () => {
	let cepAlternativa = $('#CEP').val();
	let numeroResidencia = $('#NumeroCasa').val();
	let ruaAlternativa = $('#Rua').val() ;
	let bairroAlternativa = $('#Bairro').val() ;
	let cidadeAlternativa = $('#Cidade').val();
	$.post(
		'http://localhost:8080/adicionar_dados_alternativos',
		{
			cepAlternativa: cepAlternativa,
			numeroResidencia: numeroResidencia,
			ruaAlternativa: ruaAlternativa,
			bairroAlternativa: bairroAlternativa,
			cidadeAlternativa: cidadeAlternativa
	})
})

// ================================================================================
$('#metodoEnvioCorreios').on('change' , e => {
	let metodoEnvio = document.getElementById('metodoEnvioCorreios');
	if( metodoEnvio.checked == true){
		//mostra o valor do envio
		//Cálculo do envio
		let valorEnvioCorreios = 20;
		$('#ValorEnvioCorreios').html('Valor: R$' + valorEnvioCorreios)
		$('#EnvioCorreios').css({
			"display" : "inline"
		})
		/*$.ajax({
			type: 'GET',
			url: `http://localhost:8080/metodo_correios`,
			dataType: 'json',
			success: dados => {
				console.log(dados)
			},
			erro: erro => {console.log(erro)}
		})*/
		$.post(
			'http://localhost:8080/metodo_envio',
			{
				metodo_envio: 'Correios',
				valor_envio: valorEnvioCorreios
			}
		)
	}else {
		//esconde o form
		$('#EnvioCorreios').css({
			"display" : "none"
		})
	}
})

// ================================================================================
$('#metodoEnvioExcursao').on('change' , e => {
	let form = document.getElementById('metodoEnvioExcursao');
	if(form.checked == true){
		//mostra o from
		$('#campoNomeExcursao').css({
			"display" : "inline"
		})
	}else {
		//esconde o form
		$('#campoNomeExcursao').css({
			"display" : "none"
		})
	}
})

$('#nomeExcursao').on('blur' , e => {
	let metodoEnvio =  'Excursão ' + $(e.target).val();
	$.post(
		'http://localhost:8080/metodo_envio',
		{
			metodo_envio: metodoEnvio,
			valor_envio: 'O cliente pagará a excursão quando for receber o pedido.'
	})
})

// ================================================================================
$('#confirmarPedido').on('click' , () => {
	let metodoEnvioCorreios = document.getElementById('metodoEnvioCorreios');
	let metodoEnvioExcursao = document.getElementById('metodoEnvioExcursao');
	if(metodoEnvioCorreios.checked == true && metodoEnvioExcursao.checked == true){
		metodoEnvioCorreios.checked = false;
		metodoEnvioExcursao.checked = false;
		$('#nomeExcursao').val('');
		$('#SelecioneUmaOpcao').modal('show')
	} else {
		if(metodoEnvioCorreios.checked == true || metodoEnvioExcursao.checked == true){
			let metodoEnvio = $('#nomeExcursao').val();
			if(metodoEnvioExcursao.checked == true && metodoEnvio.length == 0){
				$('#preencherCampoExcursao').modal('show')
			}else {
				let form = document.getElementById('enderecoAlternativo');
				if(form.checked == true && ($('#CEP').val() == '' || $('#NumeroCasa').val() == '')){
					$('#dadosAlternativos').modal('show')
				} else{
					window.location.href = 'http://localhost:8080/confirmar_pedido'
				}
				
			}
		}else{
			$('#selecionaMetodoEnvio').modal('show')
		}
	}
})

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
function enderecoAlternativo(){
	//Mostrar ou esconder o form de endereço alternativo
	// enderecoAlternativo
	let form = document.getElementById('enderecoAlternativo');
	if(form.checked == true){
		//mostra o from
		$('#campoEnderecoAlternativo').css({
			"display" : "inline"
		})
	}else {
		//Remove os dados alternativos da sessão
		$.post(
			'http://localhost:8080/remover_dados_alternativos',
			{
				remover: true
		})
		//Limpa o form
		$('#CEP').val('');
		$('#NumeroCasa').val('');
		$('#Rua').val('');
		$('#Bairro').val('');
		$('#Cidade').val('');

		//esconde o form
		$('#campoEnderecoAlternativo').css({
			"display" : "none"
		})
	}
}

// ================================================================================
$('#closeModalEstoqueInvalido').on('click' , () => {
	$('#modalEstoqueInvalido').modal('toggle')
})

// ================================================================================
$('#buttonModalLimpar').on('click', () => {
	$('#showModalLimpar').modal('show');
})

// ================================================================================
$('#closeModalLimpar').on('click', () => {
	$('#showModalLimpar').modal('toggle');
})

// ================================================================================
$('#closeSelecionaMetodoEnvio').on('click', () => {
	$('#selecionaMetodoEnvio').modal('toggle');
})

// ================================================================================
$('#closePreencherCampoExcursao').on('click', () => {
	$('#preencherCampoExcursao').modal('toggle');
})

// ================================================================================
$('#closeSelecioneUmaOpcao').on('click', () => {
	$('#SelecioneUmaOpcao').modal('toggle');
})

// ================================================================================
$('#closeDadosAlternativos').on('click', () => {
	$('#dadosAlternativos').modal('toggle');
})
