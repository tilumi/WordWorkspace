class SessionsController < ApplicationController
  def create
    @auth = Authorization.create_by_omniauth(auth_hash, current_user)
    if @auth.user
       sign_in_and_redirect :user, @auth.user
    else
      raise do
        logger.info "auth_hash: #{auth_hash.inspect}"
        logger.info "@auth: #{@auth.errors.inspect}"
        redirect_to new_user_session_path
      end
    end
  end

  def failure
    flash[:error] = params[:message]
    redirect_to new_user_session_path
  end

  private

  def auth_hash
    request.env['omniauth.auth']
  end
end