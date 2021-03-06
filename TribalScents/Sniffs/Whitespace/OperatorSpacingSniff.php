<?php
namespace TribalScents\Sniffs\Whitespace;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Modified version of Squiz operator white spacing, based upon Squiz code
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Greg Sherwood <gsherwood@squiz.net>
 * @author   Marc McIntyre <mmcintyre@squiz.net>
 */
class OperatorSpacingSniff implements Sniff
{
	/**
	 * A list of tokenizers this sniff supports.
	 *
	 * @var array
	 */
	public $supportedTokenizers = array(
		'PHP',
		'JS',
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		$comparison = Tokens::$comparisonTokens;
		$operators  = Tokens::$operators;
		$assignment = Tokens::$assignmentTokens;

		return array_unique( array_merge( $comparison, $operators, $assignment ) );

	}//end register()


	/**
	 * Processes this sniff, when one of its tokens is encountered.
	 *
	 * @param File $phpcsFile The current file being checked.
	 * @param int  $stackPtr  The position of the current token in the
	 *                        stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( File $phpcsFile, $stackPtr )
	{
		$tokens = $phpcsFile->getTokens();

		if ( $tokens[ $stackPtr ][ 'code' ] === T_EQUAL )
		{
			// Skip for '=&' case.
			if ( isset( $tokens[ ( $stackPtr + 1 ) ] ) === true && $tokens[ ( $stackPtr + 1 ) ][ 'code' ] === T_BITWISE_AND )
			{
				return;
			}//end if

			// Skip default values in function declarations.
			if ( isset( $tokens[ $stackPtr ][ 'nested_parenthesis' ] ) === true )
			{
				$bracket = end( $tokens[ $stackPtr ][ 'nested_parenthesis' ] );
				if ( isset( $tokens[ $bracket ][ 'parenthesis_owner' ] ) === true )
				{
					$function = $tokens[ $bracket ][ 'parenthesis_owner' ];
					if ( $tokens[ $function ][ 'code' ] === T_FUNCTION )
					{
						return;
					}//end if
				}//end if
			}//end if
		}//end if

		if ( $tokens[ $stackPtr ][ 'code' ] !== T_BITWISE_AND )
		{
			if ( $tokens[ $stackPtr ][ 'code' ] === T_MINUS )
			{
				// Check minus spacing, but make sure we aren't just assigning
				// a minus value or returning one.
				$prev = $phpcsFile->findPrevious( T_WHITESPACE, ( $stackPtr - 1 ), null, true );
				if ( $tokens[ $prev ][ 'code' ] === T_RETURN )
				{
					// Just returning a negative value; eg. return -1.
					return;
				}//end if

				if ( in_array( $tokens[ $prev ][ 'code' ], Tokens::$operators ) === true )
				{
					// Just trying to operate on a negative value; eg. ( $var * -1 ).
					return;
				}//end if

				if ( in_array( $tokens[ $prev ][ 'code' ], Tokens::$comparisonTokens ) === true )
				{
					// Just trying to compare a negative value; eg. ( $var === -1 ).
					return;
				}//end if

				// A list of tokens that indicate that the token is not
				// part of an arithmetic operation.
				$invalidTokens = array(
					T_COMMA,
					T_OPEN_PARENTHESIS,
					T_OPEN_SQUARE_BRACKET,
				);

				if ( in_array( $tokens[ $prev ][ 'code' ], $invalidTokens ) === true )
				{
					// Just trying to use a negative value; eg. myFunction( $var, -2 ).
					return;
				}//end if

				$number = $phpcsFile->findNext( T_WHITESPACE, ( $stackPtr + 1 ), null, true );
				if ( $tokens[ $number ][ 'code' ] === T_LNUMBER )
				{
					$semi = $phpcsFile->findNext( T_WHITESPACE, ( $number + 1 ), null, true );
					if ( $tokens[ $semi ][ 'code' ] === T_SEMICOLON )
					{
						if ( $prev !== false && ( in_array( $tokens[ $prev ][ 'code' ], Tokens::$assignmentTokens ) === true ) )
						{
							// This is a negative assignment.
							return;
						}//end if
					}//end if
				}//end if
			}//end if

			$operator = $tokens[ $stackPtr ][ 'content' ];

			if ( $tokens[ ( $stackPtr - 1 ) ][ 'code' ] !== T_WHITESPACE )
			{
				$error = "Expected 1 space before \"$operator\"; 0 found";
				$phpcsFile->addError( $error, $stackPtr, 'invalidWhitespace' );
			}//end if
			elseif ( strlen( $tokens[ ( $stackPtr - 1 ) ][ 'content' ] ) !== 1 && "\t" !== substr( $tokens[ ( $stackPtr - 1 ) ][ 'content' ], -1 ) )
			{
				// Don't throw an error for assignments, because other standards allow
				// multiple spaces there to align multiple assignments.
				if ( in_array( $tokens[ $stackPtr ][ 'code' ], Tokens::$assignmentTokens ) === false )
				{
					$found = strlen( $tokens[ ( $stackPtr - 1 ) ][ 'content' ] );
					$error = "Expected 1 space before \"$operator\"; $found found";
					$phpcsFile->addError( $error, $stackPtr, 'invalidWhitespace' );
				}//end if
			}//end elseif

			if ( $operator !== '-' )
			{
				if ( $tokens[ ( $stackPtr + 1 ) ][ 'code' ] !== T_WHITESPACE )
				{
					$error = "Expected 1 space after \"$operator\"; 0 found";
					$phpcsFile->addError( $error, $stackPtr, 'invalidWhitespace' );
				}//end if
				elseif ( strlen( $tokens[ ( $stackPtr + 1 ) ][ 'content' ] ) !== 1 )
				{
					$found = strlen( $tokens[ ( $stackPtr + 1 ) ][ 'content' ] );
					$error = "Expected 1 space after \"$operator\"; $found found";
					$phpcsFile->addError( $error, $stackPtr, 'invalidWhitespace' );
				}//end elseif
			}//end if
		}//end if
	}//end process
}//end class
