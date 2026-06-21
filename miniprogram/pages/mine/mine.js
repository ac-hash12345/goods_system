// mine.js - 个人中心页面逻辑

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