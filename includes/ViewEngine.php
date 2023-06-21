<?php
namespace WmycBogo {
    use stdClass;

	class ViewEngine {
		public static function render($viewFile, stdClass $data) {
			ob_start();
			require WMYC_BOGO_PLUGIN_VIEWS . '/' . $viewFile;
			return ob_get_clean();
		}
	}
}