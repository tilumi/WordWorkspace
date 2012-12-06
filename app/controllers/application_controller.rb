class ApplicationController < ActionController::Base
  protect_from_forgery
  # include AuthenticationHelper

  def after_sign_in_path_for(user)
    request.env['omniauth.origin'] || stored_location_for(user) || users_path(user)
  end

  def after_sign_out_path_for(resource_or_scope)
    request.referrer
  end
  
end
