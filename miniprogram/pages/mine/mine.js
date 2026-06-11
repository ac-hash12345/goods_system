const { getUserInfo } = require('../../utils/storage');

Page({
  data: {
    userInfo: null
  },

  onShow() {
    this.setData({ userInfo: getUserInfo() });
  },

  toLogin() {
    wx.navigateTo({ url: '/pages/login/login' });
  }
});