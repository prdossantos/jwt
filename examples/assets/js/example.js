;(function(window,document){
	var obj = [];

	if( localStorage.getItem('aq_token') ) {
		document.getElementById('token').innerHTML = localStorage.getItem('aq_token')
		document.getElementById('title').innerHTML = 'Token gerado'
	}

	var xhr = function(options)
	{

		let opt = options;
			opt.method = options.method || 'post'
			opt.url = options.url || ''
			opt.data = options.data || ''
			opt.callback = options.callback || false
			opt.header = options.header || false

		let xhr = new XMLHttpRequest();
			xhr.open(opt.method, opt.url, true)
			if(opt.method.toLowerCase() == 'post')
				xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
			if(opt.header) {
				Object.keys(opt.header).forEach( function(index) {
					xhr.setRequestHeader(index,opt.header[index])
				});
			}


			xhr.onload = function(res){
				obj = res.target.responseText
				if(opt.callback)
					opt.callback(obj);
			}
			xhr.send(opt.data);
	}

	var getToken = function(callback)
	{
		xhr({
			'method':'post',
			'url':'/testes/jwt/examples/api/',
			'data':'id=6546',
			'callback': callback
		})
	}


	var button_get_token = document.getElementById('get_token');
		button_get_token.addEventListener('click', function(){
 			localStorage.removeItem('aq_token')
 			getToken(function(res){
 				obj = JSON.parse(res)

 				var k = obj.data

 				localStorage.setItem('aq_token',k);
 				document.getElementById('title').innerHTML = 'Token gerado'
 				document.getElementById('token').innerHTML = k

 			});	
		})

	var get_data = document.getElementById('get_data');
		get_data.addEventListener('click',function(){
			var k = localStorage.getItem('aq_token');
			xhr({
				'method':'get',
				'url':'/testes/jwt/examples/api/?cod=6546',
				'data':'id=6546',
				'header':{
					'Authorization': 'Bearer '+k
				},
				'callback': function(res){
			 		obj = JSON.parse(res)
					document.getElementById('data').innerHTML = '<hr>'+obj.msg 
				}
			})
		});



}(window,document));