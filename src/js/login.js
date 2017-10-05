(function() {
	Vue.config.devtools = false;
	Vue.use(Vue.resource);
	var vm = new Vue({
		el: 'body',
		data: {
			mainData: [],
			status: {
				isLoading: true,
				isSubmit: false
			},
			hintData: {
				status: false,
				text: ''
			},
			orgList: [],
			form: {
				name: '',
				check: '',
				org: 52
			}
		},
		ready: function() {
			this.getData();
			this.status.isLoading = false;

		},
		methods: {
			getData: function() {
				this.$http.get('../phpCtrl/org.php').then(function(res){
					console.log(res)
					this.$set('orgList', res.json().data);
				});
			},
			offHint: function() {
				this.hintData.status = false;
			},
			onSubmit: function() {
				this.status.isSubmit = true;
				this.$http.post('../phpCtrl/login.php', {
					'name': this.form.name,
					'check': this.form.check,
					'org': this.form.org
				}, {
					emulateJSON:true,
					headers:{
						'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'	
					}
				}).then(function(res) {
					this.status.isSubmit = false;
					if (res.json().result == 1) {
						location.href = './overview.html';

					} else {
						this.hintData.text = res.json().msg;
						this.hintData.status = true;
					}
				});
			}
		}
	});
})();

