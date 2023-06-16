var COMPONENTS_URL = (window.location.href.indexOf("?")==-1)?"/components":"components";
function save_positions(positions) {
  $.get(COMPONENTS_URL + "/pages_admin/ajax.php?action=save_positions", {positions:positions}, function(data) { provest(id, data); });
}
function save_col_positions(positions) {
  $.get(COMPONENTS_URL + "/pages_admin/ajax.php?action=save_col_positions", {positions:positions}, function(data) { provest(id, data); });
}
function openID(mail) {
  $.get(COMPONENTS_URL + "/pages_index/ajax.php?action=openID", {mail:mail}, function(data) {
    $('#acc_openID_avatar').prop("src", "http://www.gravatar.com/avatar/" + $(data).filter("#avatar").text() + ".png?d=mm&s=45");
  });
}
function online_users_expand() {
  $.get(COMPONENTS_URL + "/pages_index/ajax.php?action=online_users_expand", function(data) { provest('online_users', data); });
}
function check_login_nick(nick) {
  if (nick!="") { $.get(COMPONENTS_URL + "/pages_index/ajax.php?action=check_login_nick", {nick:nick}, function(data) { if (data.indexOf("neni")!=-1) { provest('login_form', data); } }); }
}
function update_comment(id) {
  if (id!=0) { $.get(COMPONENTS_URL + "/pages_index/ajax.php?action=update_comment", {id:id}, function(data) { provest(id, data); }); }
}
function save_comment(id, text) {
  if (id!=0) { $.post(COMPONENTS_URL + "/pages_index/ajax.php?action=save_comment", {id:id, text:text}, function(data) { provest(id, data); }); }
}
function delete_comment(id) {
  if (id!=0) { $.get(COMPONENTS_URL + "/pages_index/ajax.php?action=delete_comment", {id:id}, function() { provest("div" + id, ""); }); }
}
function hide_pm(id) {
  $.get(COMPONENTS_URL + "/pages_index/ajax.php?action=hide_pm", {id:id}, function() { provest("tr" + id, ""); });
}
function seen(id, checked) {
  $.get(COMPONENTS_URL + "/pages_index/ajax.php?action=seen", {checked:checked, id:id});
}
function pm_reciever(id) {
  $.get(COMPONENTS_URL + "/pages_index/ajax.php?action=reciever", {reciever:id}, function(data) { provest("reciever", data); });
}
function provest(id, data) {
  $('#' + id).empty().append(data);
}