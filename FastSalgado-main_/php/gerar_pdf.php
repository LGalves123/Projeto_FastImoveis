<?php
// Use o caminho correto para incluir o autoload.php 
require_once __DIR__ . '/../vendor/autoload.php';

// Crie uma nova instância do TCPDF
$pdf = new TCPDF();

// Defina informações básicas do documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Seu Nome');
$pdf->SetTitle('Título do Documento');
$pdf->SetSubject('Assunto do Documento');
$pdf->SetKeywords('TCPDF, PDF, exemplo, teste, guia');

// Adicione uma página
$pdf->AddPage();

// Defina o conteúdo do PDF 
$html = ' <h1 style="text-align: center;">MODELO DE CONTRATO DE COMPRA E VENDA À VISTA</h1> 
<p>Por este instrumento particular, as partes qualificadas na Cláusula 1ª têm entre si justa e acertada a presente relação contratual.</p> 
<h3>CLÁUSULA 1ª - QUALIFICAÇÃO DAS PARTES</h3> 
<p><strong>Vendedor</strong>
<br> Nome ou Razão Social: ___________________________
<br> Nacionalidade (se pessoa física): ___________________________
<br> Estado Civil (se pessoa física): ___________________________
<br> Profissão (se pessoa física): ___________________________<br> 
Identidade (se pessoa física): ___________________________
<br> CPF ou CNPJ: ___________________________
<br> Endereço: ___________________________
<br></p> 
<p><strong>Se o vendedor for casado, preencha os campos abaixo, caso contrário apague os mesmos. 
Em qualquer caso, apague esta frase ao final.</strong></p> <p><strong>Cônjuge do Vendedor</strong>
<br> Nome: ___________________________
<br> Nacionalidade: ___________________________
<br> Profissão: ___________________________
<br> Identidade: ___________________________
<br> CPF: ___________________________
<br></p> <p><strong>Comprador</strong>
<br> Nome ou Razão Social: ___________________________
<br> Nacionalidade (se pessoa física): ___________________________
<br> Estado Civil (se pessoa física): ___________________________
<br> Profissão (se pessoa física): ___________________________
<br> Identidade (se pessoa física): ___________________________
<br> CPF ou CNPJ: ___________________________
<br> Endereço: ___________________________
<br></p> <p><strong>Se o comprador for casado, preencha os campos abaixo, caso contrário apague os mesmos. 
Em qualquer caso, apague esta frase ao final.</strong></p> <p><strong>Cônjuge do Comprador</strong>
<br> Nome: ___________________________
<br> Nacionalidade: ___________________________
<br> Profissão: ___________________________
<br> Identidade: ___________________________
<br> CPF: ___________________________
<br></p> <h3>CLÁUSULA 2ª - O presente contrato tem por finalidade a comercialização do imóvel descrito a seguir, de propriedade do VENDEDOR:</h3> 
<p>Endereço do Imóvel: ___________________________
<br> Número de Matrícula no Cartório de Registro de Imóveis: ___________________________<br></p> 
<h3>CLÁUSULA 3ª</h3> <p>Pelo presente instrumento e na melhor forma de direito, o VENDEDOR tem ajustado vender, 
conforme promete ao COMPRADOR, e esse comprar-lhe, o imóvel descrito e caracterizado na Cláusula 2ª, 
que possue de forma livre e desembaraçada de quaisquer ônus real, pessoal, fiscal ou extrajudicial, 
dívidas, arrestos ou seqüestros, ou, ainda, de restrições de qualquer natureza, 
pelo preço e de conformidade com as cláusulas ora estabelecidas.</p> <h3>CLÁUSULA 4ª</h3> 
<p>O preço certo e ajustado da venda ora acertada é de R$ ___________________________ ([Clique aqui e digite o valor por extenso]), 
por conta do qual o VENDEDOR confessa e declara haver recebido do COMPRADOR o valor de R$ ___________________________ ([Clique aqui e digite o valor por extenso]), 
a título de sinal de negócio e princípio de pagamento, conforme recibo assinado pelo VENDEDOR e que, na época do pagamento, 
foi entregue aos COMPRADOR e de cujo recebimento dão a mais ampla quitação.</p> <p>Parágrafo único - O restante do preço, no valor de R$ ___________________________ ([Clique aqui e digite o valor por extenso]), 
será pago pelo COMPRADOR da seguinte forma:<br> [Clique aqui e digite as condições de pagamento]</p> 
<h3>CLÁUSULA 5ª</h3> <p>A posse do imóvel objeto deste contrato é transmitida pelo VENDEDOR ao COMPRADOR neste ato, situação essa representada pela entrega das chaves do referido imóvel.</p> 
<h3>CLÁUSULA 6ª</h3> <p>O VENDEDOR obriga-se a outorgar ao COMPRADOR, ou em nome de quem por ele for indicado ou que ainda legalmente o represente, a competente Escritura Definitiva de Compra e Venda do imóvel descrito na Cláusula 2ª, 
totalmente livre e desembaraçado de quaisquer ônus ou gravames de qualquer natureza, no prazo máximo de [Clique aqui e digite] dias, 
contados da data de assinatura deste contrato.</p> <p>Parágrafo único - A recusa do VENDEDOR em outorgar a escritura Definitiva de que trata esta Cláusula dará ao COMPRADOR o direito de pedir adjudicação compulsória do imóvel, além de perdas e danos que venham a ser causados em razão da citada recusa.</p> 
<h3>CLÁUSULA 7ª</h3> <p>A partir da data de assinatura do presente contrato, correrão por conta exclusiva do COMPRADOR todos os impostos, taxas ou contribuições fiscais de qualquer natureza incidentes sobre o imóvel, ainda que lançados em nome do VENDEDOR ou de terceiros, assim como serão, desde já, 
de sua inteira responsabilidade as despesas com o registro deste contrato e da Escritura Definitiva no Cartório de Registro de Imóveis, emolumentos notariais e outros, inclusive o pagamento do Imposto de Transmissão de Bens Imóveis ITBI.</p> 
<h3>CLÁUSULA 8ª</h3> 
<p>O COMPRADOR poderá ceder ou transferir os direitos que lhe decorre deste contrato, independentemente de anuência do VENDEDOR, ficando cedentes e cessionários solidários no cumprimento das obrigações ora ajustadas.</p> 
<h3>CLÁUSULA 9ª</h3> 
<p>O presente contrato é celebrado sob a condição expressa de sua irrevogabilidade e irretratabilidade, ressalvando o eventual inadimplemento do COMPRADOR, renunciando os contratantes, expressamente, à faculdade de arrependimento concedida pelo art. 420 do Código Civil.</p> 
<h3>CLÁUSULA 10ª</h3> 
<p>Para todos os fins de direito, os contratantes declaram aceitar o presente contrato nos expressos termos em que foi lavrado, obrigando-se a si, seus herdeiros e sucessores a bem fielmente cumpri-lo.</p> 
<h3>CLÁUSULA 11ª</h3> 
<p>Fica o Registro de Imóveis autorizado, mediante solicitação, a promover o registro do presente instrumento, na forma legal.</p> 
<h3>CLÁUSULA 12ª</h3> 
<p>As partes elegem o Foro da Comarca de ___________________________ para dirimir qualquer dúvida sobre este instrumento.</p> 
<p>E por estarem assim justas e contratadas as partes assinam o presente contrato em ___________________________ vias de igual teor e forma, na presença de testemunhas.</p> 
<p>CIDADE-UF, __ de _________ de 201_</p> 
<p>____________________________<br>Vendedor</p> 
<p>____________________________<br>Cônjuge</p> 
<p>____________________________<br>Comprador</p> 
<p>____________________________<br>Cônjuge</p> 
<p>____________________________<br>Testemunha 1<br>CPF: ___________________________</p> 
<p>____________________________<br>Testemunha 2<br>CPF: ___________________________</p> ';

// Escreva o HTML no PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Saída do PDF
$pdf->Output('exemplo.pdf', 'I');
?>
