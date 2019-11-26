require 'addressable'
require 'nokogiri'

def makeLinkRelative( elt, attributeName, currentUrl )
  oldLink = Addressable::URI.parse("https://grima.zemkat.org/dist" + elt.get_attribute( attributeName ) )
  newLink = currentUrl.route_to( oldLink )
  elt.set_attribute( attributeName, newLink )
  #puts( ""+ currentUrl+ " -> "+ oldLink+ " = "+ newLink )
end

Dir.glob("**/*html").each do |file|
  here = Addressable::URI.parse("https://grima.zemkat.org/dist/" + (File.dirname file) + "/")
  doc = Nokogiri::HTML(open(file))
  doc.css('a[href^="/"]').each do |elt|
    makeLinkRelative( elt, "href", here )
  end
  doc.css('link[href^="/"]').each do |elt|
    makeLinkRelative( elt, "href", here )
  end
  doc.css('script[src^="/"]').each do |elt|
    makeLinkRelative( elt, "src", here )
  end
  doc.css('img[src^="/"]').each do |elt|
    makeLinkRelative( elt, "src", here )
  end
  File.open(file, 'w') { |file| file.write(doc) }
end
