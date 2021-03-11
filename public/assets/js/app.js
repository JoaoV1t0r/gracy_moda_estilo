// ================================================================================
function adicionarCarrinho(id_produto){
	$.ajax({
		type: 'GET',
		url: `/adiconar_carrinho`,
		data: `id_produto=${id_produto}`,
		dataType: 'json',
		success: dados => {
			$('#carrinho').html(dados)
		},
		erro: erro => {console.log(erro)}
	})
}