App({
  globalData: {
    apiBase: 'http://localhost/goodsSystem/backend/api',
    userInfo: null
  },

  onLaunch() {
    const cachedUser = wx.getStorageSync('userInfo');
    if (cachedUser) {
      this.globalData.userInfo = cachedUser;
    }
  }
});