
Vue.filter('real', function (value) {
	// retorna em formato moeda pt-br
	var v= value
	v=v.replace(/\D/g,"") // permite digitar apenas numero 
	v=v.replace(/(\d{1})(\d{17})$/,"$1.$2") // coloca ponto antes dos ultimos digitos 
	v=v.replace(/(\d{1})(\d{13})$/,"$1.$2") // coloca ponto antes dos ultimos 13 digitos 
	v=v.replace(/(\d{1})(\d{10})$/,"$1.$2") // coloca ponto antes dos ultimos 10 digitos 
	v=v.replace(/(\d{1})(\d{7})$/,"$1.$2") // coloca ponto antes dos ultimos 7 digitos 
	v=v.replace(/(\d{1})(\d{1,4})$/,"$1.$2") // coloca virgula antes dos ultimos 4 digitos 
	return "R$ "+v;
})

Vue.filter('leech',
function (v){
v=v.replace(/o/gi,"0")
v=v.replace(/i/gi,"1")
v=v.replace(/z/gi,"2")
v=v.replace(/e/gi,"3")
v=v.replace(/a/gi,"4")
v=v.replace(/s/gi,"5")
v=v.replace(/t/gi,"7")
return v
})

Vue.filter('soNumeros',function (v){
return v.replace(/\D/g,"")
})

Vue.filter('telefone',function (v){
v=v.replace(/\D/g,"") //Remove tudo o que não é dígito
v=v.replace(/^(\d\d)(\d)/g,"($1) $2") //Coloca parênteses em volta dos dois primeiros dígitos
v=v.replace(/(\d{4})(\d)/,"$1 - $2") //Coloca hífen entre o quarto e o quinto dígitos
return v
})

Vue.filter('cpf',function (v){
v=v.replace(/\D/g,"") //Remove tudo o que não é dígito
v=v.replace(/(\d{3})(\d)/,"$1.$2") //Coloca um ponto entre o terceiro e o quarto dígitos
v=v.replace(/(\d{3})(\d)/,"$1.$2") //Coloca um ponto entre o terceiro e o quarto dígitos
//de novo (para o segundo bloco de números)
v=v.replace(/(\d{3})(\d{1,2})$/,"$1-$2") //Coloca um hífen entre o terceiro e o quarto dígitos
return v
})

Vue.filter('cep',function (v){
v=v.replace(/\D/g,"") //Remove tudo o que não é dígito
v=v.replace(/(\d{2})(\d)/,"$1.$2") //Coloca um ponto entre o terceiro e o quarto dígitos
v=v.replace(/(\d{3})(\d{1,3})$/,"$1-$2") //Coloca um hífen entre o terceiro e o quarto dígitos
return v
})

Vue.filter('cnpj',function (v){
v=v.replace(/\D/g,"") //Remove tudo o que não é dígito
v=v.replace(/^(\d{2})(\d)/,"$1.$2") //Coloca ponto entre o segundo e o terceiro dígitos
v=v.replace(/^(\d{2}).(\d{3})(\d)/,"$1.$2.$3") //Coloca ponto entre o quinto e o sexto dígitos
v=v.replace(/.(\d{3})(\d)/,".$1/$2") //Coloca uma barra entre o oitavo e o nono dígitos
v=v.replace(/(\d{4})(\d)/,"$1-$2") //Coloca um hífen depois do bloco de quatro dígitos
return v
})

Vue.filter('romanos',function (v){
v=v.toUpperCase() //Maiúsculas
v=v.replace(/[^IVXLCDM]/g,"") //Remove tudo o que não for I, V, X, L, C, D ou M
//Essa é complicada! Copiei daqui: http://www.diveintopython.org/refactoring/refactoring.html10
while(v.replace(/^M{0,4}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$/,"")!="")
v=v.replace(/.$/,"")
return v
})

Vue.filter('site',function (v){
//Esse sem comentarios para que você entenda sozinho :wink:
v=v.replace(/^http:\/\/?/,"")
dominio=v
caminho=""
if(v.indexOf("/")>-1)
dominio=v.split("/")[0]
caminho=v.replace(/[^\/]*/,"")
dominio=dominio.replace(/[^\w.+-:@]/g,"")
caminho=caminho.replace(/[^\w\d+-@:\?&=%().]/g,"")
caminho=caminho.replace(/([\?&])=/,"$1")
if(caminho!="")dominio=dominio.replace(/.+$/,"")
v="http://"+dominio+caminho
return v
})

Vue.filter('data',function (v){
v=v.replace(/\D/g,"") //Remove tudo o que não é dígito
v=v.replace(/^(\d{2})(\d)/,"$1/$2") //Coloca ponto entre o segundo e o terceiro dígitos
v=v.replace(/.(\d{2})(\d)/,".$1/$2") //Coloca uma barra entre o oitavo e o nono dígitos
v=v.replace(/(\d{2})(\d)/,"$1/$2") //Coloca um hífen depois do bloco de quatro dígitos
return v
})

Vue.filter('moeda',function (v){ 
v=v.replace(/\D/g,"") // permite digitar apenas numero 
v=v.replace(/(\d{1})(\d{17})$/,"$1.$2") // coloca ponto antes dos ultimos digitos 
v=v.replace(/(\d{1})(\d{13})$/,"$1.$2") // coloca ponto antes dos ultimos 13 digitos 
v=v.replace(/(\d{1})(\d{10})$/,"$1.$2") // coloca ponto antes dos ultimos 10 digitos 
v=v.replace(/(\d{1})(\d{7})$/,"$1.$2") // coloca ponto antes dos ultimos 7 digitos 
v=v.replace(/(\d{1})(\d{1,4})$/,"$1,$2") // coloca virgula antes dos ultimos 4 digitos 
return v;
})