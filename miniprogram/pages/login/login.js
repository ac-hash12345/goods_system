const { request } = require('../../utils/request');
const { setUserInfo } = require('../../utils/storage');

Page({
  data: {
    username: '',
    password: ''
  },

  onUsernameInput(e) {
    this.setData({ username: e.detail.value });
  },

  onPasswordInput(e) {
    this.setData({ password: e.detail.value });
  },

  loginSubmit() {
    const { username, password } = this.data;
    
    if (!username || !password) {
      wx.showToast({ title: '账号和密码不能为空', icon: 'none' });
      return;
    }

    wx.showLoading({ title: '鉴权中...', mask: true });

    request({
      url: 'login.php',
      method: 'POST',
      data: {
        username: username,
        password: password
      }
    }).then((res) => {
      wx.hideLoading();
      setUserInfo(res.data.user);
      wx.showToast({ title: '登录成功', icon: 'success' });
      
      setTimeout(() => {
        wx.navigateBack({ delta: 1 });
      }, 1000);
    }).catch((err) => {
      wx.hideLoading();
      // 🌟 核心修复：延迟 100 毫秒弹出，防止被 hideLoading 吞掉
      setTimeout(() => {
        wx.showToast({ 
          title: err.msg || '账号或密码错误', 
          icon: 'error', 
          duration: 2000 
        });
      }, 100);
    });
  }
});