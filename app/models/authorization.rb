class Authorization < ActiveRecord::Base
  attr_accessible :provider, :uid, :user_id, :user

  PROVIDERS = [:facebook]

  belongs_to :user
  validates_presence_of :user_id, :uid, :provider
  validates_uniqueness_of :uid, :scope => :provider

  def self.create_by_omniauth(hash, current_user = nil)
    hash = ActiveSupport::HashWithIndifferentAccess.new hash
    # logger.debug hash.inspect()
    auth = find_from_hash(hash)    
    unless auth
      current_user ||= User.create_from_auth!(hash)
      auth = Authorization.new(:user=>current_user,:uid=>hash[:uid],:provider=>hash[:provider])
      auth.save
    end
    created_hash = {:email => hash[:info][:email], :first_name => hash[:info][:first_name], 
                    :last_name => hash[:info][:last_name], :image => hash[:info][:image] }
    auth.user.update_attributes(created_hash)
    auth
  end

  private

  def self.find_from_hash(hash)
    if hash[:provider] && hash[:uid]
      find_by_provider_and_uid(hash[:provider],hash[:uid])
    end
  end

end
