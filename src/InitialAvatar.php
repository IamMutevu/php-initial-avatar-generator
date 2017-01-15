<?php namespace LasseRafn\InitialAvatarGenerator;

use Intervention\Image\Image;
use Intervention\Image\ImageCache;
use Intervention\Image\ImageManager;

class InitialAvatar
{
	/** @var ImageManager */
	private $image;

	private $parameter_cacheTime = 0;
	private $parameter_name      = 'JD';
	private $parameter_size      = 48;
	private $parameter_bgColor   = '#000';
	private $parameter_fontColor = '#fff';
	private $parameter_fontFile  = '/fonts/OpenSans-Regular.ttf';

	public function __construct()
	{
		$this->image = new ImageManager();
	}

	public function name( string $nameOrInitials ): self
	{
		$this->parameter_name = $this->generateInitials( $nameOrInitials );

		return $this;
	}

	public function size( int $size ): self
	{
		$this->parameter_size = (int) $size;

		return $this;
	}

	public function background( string $background ): self
	{
		$this->parameter_bgColor = (string) $background;

		return $this;
	}

	public function color( string $color ): self
	{
		$this->parameter_fontColor = (string) $color;

		return $this;
	}

	public function font( string $font ): self
	{
		$this->parameter_fontFile = (string) $font;

		return $this;
	}

	public function cache( int $minutes = 60 ): self
	{
		$this->parameter_cacheTime = (int) $minutes;

		return $this;
	}

	/**
	 * Generate the image
	 *
	 * @param null|string $name
	 *
	 * @return Image
	 */
	public function generate( $name = null ): Image
	{
		if ( $name !== null )
		{
			$this->parameter_name = $this->generateInitials( $name );
		}

		$fontFile = $this->parameter_fontFile;
		$size     = $this->parameter_size;
		$color    = $this->parameter_fontColor;
		$bgColor  = $this->parameter_bgColor;
		$name     = $this->parameter_name;

		$img = $this->image->cache( function ( ImageCache $image ) use ( $size, $bgColor, $color, $fontFile, $name )
		{
			$image->canvas( $size, $size, $bgColor )->text( $name, $size / 2, $size / 2, function ( $font ) use ( $size, $color, $fontFile )
			{
				$font->file( __DIR__ . $fontFile );
				$font->size( $size / 2 );
				$font->color( $color );
				$font->align( 'center' );
				$font->valign( 'center' );
			} );
		}, $this->parameter_cacheTime, true );

		return $img;
	}

	/**
	 * Will return the generated initials
	 *
	 * @return string
	 */
	public function getParameterName()
	{
		return $this->parameter_name;
	}

	/**
	 * Generate a two-letter initial from a name,
	 * and if no name, assume its already initials.
	 * For safety, we limit it to two characters,
	 * in case its a single, but long, name.
	 *
	 * @param string $nameOrInitials
	 *
	 * @return string
	 */
	private function generateInitials( string $nameOrInitials = 'John Doe' ): string
	{
		$nameOrInitials = mb_strtoupper( trim( $nameOrInitials ) );

		$names = explode( ' ', $nameOrInitials );

		if ( count( $names ) > 1 )
		{
			$firstNameLetter = mb_substr( $names[0], 0, 1 );
			$lastNameLetter  = mb_substr( $names[ count( $names ) - 1 ], 0, 1 );

			$nameOrInitials = "{$firstNameLetter}{$lastNameLetter}";
		}

		$nameOrInitials = mb_substr( $nameOrInitials, 0, 2 );

		return $nameOrInitials;
	}
}