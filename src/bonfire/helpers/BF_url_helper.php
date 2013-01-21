<?php

/**
 * Header redirect by named route.
 *
 * @param  string $name The name of the route to redirect to.
 * @param	string	the method: location or redirect
 */
if (!function_exists('redirect_to_route'))
{
	function redirect_to_route($name, $method = 'location', $http_response_code = 302)
	{
		$route = Route::named_url($name);

		if ($route)
		{
			redirect($route, $method, $http_response_code);
		}
	}
}

//--------------------------------------------------------------------