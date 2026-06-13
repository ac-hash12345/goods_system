App({
  globalData: {
    apiBase: 'http://127.0.0.1/goodsSystem/backend/api',
    userInfo: null
  },

  onLaunch() {
    const cachedUser = wx.getStorageSync('userInfo');
    if (cachedUser) {
      this.globalData.userInfo = cachedUser;
    }
  }
});