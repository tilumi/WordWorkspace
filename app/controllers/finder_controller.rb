class FinderController < ApplicationController

  before_filter :require_login

	skip_before_filter :verify_authenticity_token, :only => ['elfinder']

	def index
    if params[:root]
      session[:root] = File.join(params[:root])
    else
      session[:root] = File.join('/Library', 'WowzaMediaServer-3.5.0', 'content')
    end
    # binding.pry
    render :text => "Directory not exists"  unless File.exists?(session[:root])
  end

  def elfinder
    # binding.pry
    # session[:root] = File.join('/Library', 'WowzaMediaServer-3.5.0', 'content')
    # session[:root] = File.join('/Users/MingFu')
    h, r = ElFinder::Connector.new(
      :root => session[:root],
      # :root => File.join(Rails.public_path, 'img'),
      # :url => '/img',
      :url => 'rtmp://localhost/vod/mp4:',
      :perms => {
         /.*/ => {:read => true, :write => false, :locked => false}, 
         /.DS_Store$|.gif$/ => {:hidden => true, :write => false, :rm => false, :read => false},
         
       },
       :thumbs => true
    ).run(params)

    headers.merge!(h)

    render (r.empty? ? {:nothing => true} : {:text => r.to_json}), :layout => false
  end

  def image_proxy
	  query = params[:query]
	  image_url = get_image_url(query) # returns an absolute local file path or a URL
	  response.headers['Cache-Control'] = "public, max-age=#{12.hours.to_i}"
	  response.headers['Content-Type'] = 'image/jpeg'
	  response.headers['Content-Disposition'] = 'inline'
	  render :text => open(image_url, "rb").read
  end

  private

  def get_image_url(query)
	File.join(session[:root], query)  	
  end

  def require_login
    redirect_to documents_path unless current_user
  end

end
