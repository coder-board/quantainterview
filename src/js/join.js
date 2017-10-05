(function() {
	var vm = new Vue({
		el: 'body',
		data: {
			status: {
				isLoading: true,
				isSubmit: false
			},
			hintData: {
				status: false,
				text: ''
			},
			form: {
				name: '',
				pwd: '',
				repwd: '',
				depart: [],
				check: ''
			},
			modal: {
				isModal: false,
				success: '',
				text: ''
			}
		},
		ready: function() {
			this.status.isLoading = false;
		},
		methods: {
			onHint: function(text) {
				this.hintData.text = text;
				this.hintData.status = true;
			},
			offHint: function() {
				this.hintData.status = false;
			},
			onSubmit: function() {
				if (this.form.pwd.length < 6) {
					this.onHint('密码长度不得小于6位');
					return ;
				}
				if (this.form.pwd != this.form.repwd) {
					this.onHint('两次输入的密码不一致');
					return ;
				}
				this.$http.post('../phpCtrl/join.php', this.form, {
					emulateJSON: true,
					headers:{
						'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'	
					}
				}).then(function(res) {
					if (res.json().result == 1) {
						this.onModal(res.json().msg);
					} else {
						this.onHint(res.json().msg);
					}
				});
			},
			onModal: function(text, success) {
				this.modal.text = text;
				this.modal.isModal = true;
			}
		}
	});
})();