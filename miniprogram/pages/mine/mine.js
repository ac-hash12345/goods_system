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
  },

  // 👇 这里就是新加的退出登录逻辑
  handleLogout() {
    wx.showModal({
      title: '提示',
      content: '确定要退出当前账号吗？',
      confirmColor: '#ef4444',
      success: (res) => {
        if (res.confirm) {
          // 1. 清除本地缓存的 userInfo
          wx.removeStorageSync('userInfo');
          
          // 2. 清空当前页面的 data 状态，让页面变回未登录状态
          this.setData({
            userInfo: null
          });
          
          // 3. 提示用户
          wx.showToast({
            title: '已退出登录',
            icon: 'none'
          });
        }
      }
    });
  }
});