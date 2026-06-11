function setUserInfo(userInfo) {
  wx.setStorageSync('userInfo', userInfo);
}

function getUserInfo() {
  return wx.getStorageSync('userInfo') || null;
}

function setCart(cartList) {
  wx.setStorageSync('cartList', cartList);
}

function getCart() {
  return wx.getStorageSync('cartList') || [];
}

module.exports = {
  setUserInfo,
  getUserInfo,
  setCart,
  getCart
};