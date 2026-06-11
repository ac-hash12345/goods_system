const { request } = require('../../utils/request');
const { setUserInfo } = require('../../utils/storage');

Page({
  data: {
    nickname: '',
    avatarUrl: ''
  },

  onNicknameInput(e) {
    this.setData({ nickname: e.detail.value });
  },

  chooseProfile() {
    wx.getUserProfile({
      desc: '用于完善会员资料',
      success: (res) => {
        const profile = res.userInfo;
        this.setData({
          nickname: profile.nickName,
          avatarUrl: profile.avatarUrl
        });
        this.login(profile);
      }
    });
  },

  login(profile) {
    wx.login({
      success: (loginRes) => {
        request({
          url: 'login.php',
          method: 'POST',
          data: {
            code: loginRes.code,
            openid: `openid_${loginRes.code}`,
            nickname: profile.nickName || this.data.nickname,
            avatarUrl: profile.avatarUrl || this.data.avatarUrl
          }
        }).then((res) => {
          setUserInfo(res.data.user);
          wx.showToast({ title: '登录成功' });
          wx.navigateBack({ delta: 1 });
        });
      }
    });
  }
});