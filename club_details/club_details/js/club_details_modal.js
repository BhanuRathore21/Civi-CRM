modal_reload_parent = function() {
    if (Drupal.CTools.Modal.modal) {
        Drupal.CTools.Modal.dismiss();
        window.location.reload();
    }
};
Drupal.ajax.prototype.commands.modal_reload_parent = modal_reload_parent;
