<?php
/**
 * ModernTribe_Sniffs_PHP_EscjsFunctionSniff
 *
 * Throw an error if esc_js is used
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Matthew Batchelder <borkweb@gmail.com>
 * @author    Zachary Tirrell <zbtirrell@gmail.com>
 * @author    Stephen Page <stephenjpage@gmail.com>
 * @copyright 2012 ModernTribe
 * @license   https://github.com/ModernTribe/ModernTribe-codesniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.4.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class ModernTribe_Sniffs_PHP_EscjsFunctionSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff
{
	/**
	 * A list of forbidden functions with their alternatives.
	 *
	 * The value is NULL if no alternative exists. IE, the
	 * function should just not be used.
	 *
	 * @var array(string => string|null)
	 */
	protected $forbiddenFunctions = array(
		'esc_js' => 'json_encode',
	);

	/**
	 * Generates the error or wanrning for this sniff.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the forbidden function
	 *                                        in the token array.
	 * @param string               $function  The name of the forbidden function.
	 * @param string               $unused_pattern   The pattern used for the match.
	 *
	 * @return void
	 */
	protected function addError( $phpcsFile, $stackPtr, $function, $unused_pattern = NULL )
	{
		$data  = array( $function );
		$error = 'Barf out, gag me with a spoon! esc_js()!';

		if ( $this->forbiddenFunctions[ $function ] )
		{
			$error .= 'This is for inline javascript, which is against our standards. Use ' . $this->forbiddenFunctions[ $function ] . ' instead.';
		}//end if

		$type  = 'Found';

		if ( TRUE === $this->error )
		{
			$phpcsFile->addError( $error, $stackPtr, $type, $data );
		}//end if
		else
		{
			$phpcsFile->addWarning( $error, $stackPtr, $type, $data );
		}//end else
	}//end addError
}//end class
