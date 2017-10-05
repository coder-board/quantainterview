(function() {
	Vue.config.devtools = false;
	Vue.use(Vue.resource);
	var vm = new Vue({
		el: 'body',
		data: {
			status: {
				isLoading: true,
				unSaved: false
			},
			modal: {
				isModal: false,
				success: '',
				text: ''
			},
			url: {
				actionUrl: '',	// form提交的url
				freshUrl: '',	// 刷新该页面所对应的url
				backUrl: './overview.html'		// 返回到主界面所对应的url
			},
			depart: [],
			mainData: {
				'master': '',
				'name': '',
				'sex': 1,
				'itv_code': '',
				'phone': '',
				'email': '',
				'class': '',
				'other_depart': 1,
				'first_will': 2,
				'second_will': 0,
				'fir_result': '',
				'fir_comment': '',
				'fir_grade': '',
				'sec_comment': '',
				'sec_grade': '',
				'sec_result': 0
			}
		},
		ready: function() {
			this.getDepart();
			this.render();
			if (this.getParam('itv_code')) {
				this.url.actionUrl = '../phpCtrl/update.php';
			} else {
				this.url.actionUrl = '../phpCtrl/insert.php';
			}
		},
		watch: {
			'status.unSaved': function(val) {	// 当unSaved变化时触发
				window.onbeforeunload = function() {
					if (val == true) {
						event.returnValue = "页面尚未保存";
						return false;
					} 
				}
			}
		},
		methods: {
			getDepart: function() {
				this.$http.get('../phpCtrl/getFormGroup.php').then(function(res) {
					this.$set('depart', res.json().data.depart);
				});
			},
			getParam: function(param) {		//获取url上的参数
				var str = location.search;
				if (str.indexOf("?") == -1) return '';
				str = str.substr(1, str.length).split("&");
				for (var i = 0, cell, length = str.length; i < length; i++) {
					cell = str[i].split('=');
					if (cell[0] == param) {
						return cell[1];
					}
				}
			},
			render: function() {
				var require,
					param = this.getParam('itv_code');
				if (!param) json = {}
				else json = {
					emulateJSON: true,
					params: {
						'itv_code': param
					}
				};
				this.$http.get('../phpCtrl/detail.php', json).then(function(res) {
					if (res.json().result == 1) {
						this.status.isLoading = false;
						this.$set('mainData', res.json().data);
						this.url.backUrl = './overview.html?department=' + 
							res.json().data.first_will;

					} else if (res.json().result == 100) {
						this.status.isLoading = false;
						this.mainData.master = res.json().data.master;

					} else if (res.json().result == -1) {
						location.href = './login.html';

					} else {
						alert('请求出现异常，请重新登录');
						location.href = './login.html';
					}
				});
			},
			hasModified: function() {
				this.status.unSaved = true;
			},
			onSubmit: function() {
				if (!this.status.unSaved) return ;
				this.$http.post(this.url.actionUrl, this.mainData, {
					emulateJSON:true,
					headers: {
						'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'	
					}
				}).then(function(res) {
					if (res.json().result == 1) {
						this.status.unSaved = false;
						this.onModal(res.json().msg, 'success');
						if (res.json().data.first_will) {
							this.url.freshUrl = './detail.html?itv_code=' + 
								res.json().data.itv_code;
							this.url.backUrl = './overview.html?department=' + 
								res.json().data.first_will; 
						}
					} else {
						this.onModal(res.json().msg);
					}
				});
			},
			back: function() {
				location.href = this.url.backUrl;
			},
			onModal: function(text, success) {
				this.modal.success = false;
				this.modal.text = text;
				this.modal.isModal = true;
				if (success && success == 'success') {
					this.modal.success = true;
				}
			},
			offModal: function() {
				this.modal.isModal = false;
				if (this.url.actionUrl.indexOf('insert.php') != -1) {
					this.url.freshUrl && (location.href = this.url.freshUrl);
				}
			}
		}
	});

	/* 过滤器toDepartment： 数字 转 部门*/
	Vue.filter('toDepartment', function(value) {
		var json = vm.depart;
		for (var key in json) {
			if (json[key]['depart_id'] == value) {
				return json[key]['depart_name'];
			}
		}
		return '无';
	});

	/* 过滤器toResult： 数字 转 面试结果*/
	Vue.filter('toResult', function(value) {
		switch(value) {
			case '3': return '通过';
			case '2': return '待定';
			case '1': return '不通过';
			default: return '无记录';
		}
	});
})();