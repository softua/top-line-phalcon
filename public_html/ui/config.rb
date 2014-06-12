# sass 3.3.7
# compass 1.0.0
# https://github.com/aaronrussell/compass-rgbapng

# Require any additional compass plugins here.
require 'rgbapng'

# The environment mode. Defaults to :production, can also be :development
environment = :development

# Set this to the root of your project when deployed:
project_path = File.expand_path("..",File.dirname(__FILE__))
http_path = "/"
css_dir = "css"
fonts_dir = "fonts"
images_dir = "img"
javascripts_dir = "js"
sass_dir = "ui"
# output_style = :expanded or :nested or :compact or :compressed
output_style = (environment == :production) ? :compressed : :expanded
line_comments = false

# To enable relative paths to assets via compass helper functions. Uncomment:
relative_assets = true

asset_cache_buster :none

puts project_path

#asset_cache_buster do |http_path, real_path|
#  nil
#end

watch "img/**/*" do |project_dir, relative_path|
  if File.exists?(File.join(project_dir, relative_path))
    puts "File size of #{relative_path} is: #{File.size(File.join(project_dir, relative_path))}"
  end
end
