
# NULL contextual argument should be skipped during views URL generation

<h3 id="summary-problem-motivation">Problem/Motivation</h3>

I used the `NULL` contextual argument to skip the default URL argument value for rewriting it later with the custom argument_default plugin but I faced an issue with URL generation. My view except contextual filters also have exposed filters, and the problem is that exposed form action was wrong.

I investigated this issue deeper and found that this problem comes from [`\Drupal\views\Plugin\views\display\PathPluginBase::getRoute`](https://api.drupal.org/api/drupal/core%21modules%21views%21src%21Plugin%21views%21display%21PathPluginBase.php/function/PathPluginBase%3A%3AgetRoute/11.x). When we generate routes for views we include all contextual arguments into the view display URL path.

<h4 id="summary-steps-reproduce">Steps to reproduce</h4>

I have created [dummy module](https://github.com/Chizh273/views_null_argument_issue) that provides node type and view with exposed and contextual filters for reproducing this issue. So, steps to reproduce:

1. Install the [module](https://github.com/Chizh273/views_null_argument_issue) (this module will generate on install 20 nodes needed for reproducing the problem)
2. Go to the `/views-null-argument-issue/{NodeID}` (use any Node ID of `views_null_argument_issue` node type for NodeID) and check that
   - the view displays 10 items (result summary above of the form)
   - all items have the same type in the table
3. Submit the exposed form with any filter value
4. Check the following
   - The URL was changed to like this `/views-null-argument-issue/{NodeID}/exception-value?...`
   - the view displays 20 items (result summary above of the form)
   - there are `type 1` and `type 2` items


<h3 id="summary-proposed-resolution">Proposed resolution</h3>

I think the `NULL` argument should be excluded from route params in the [`\Drupal\views\Plugin\views\display\PathPluginBase::getRoute`](https://api.drupal.org/api/drupal/core%21modules%21views%21src%21Plugin%21views%21display%21PathPluginBase.php/function/PathPluginBase%3A%3AgetRoute/11.x) to fix this issue.

We can exclude it by creating a new `SkipFromRouteParamsInterface` interface, and use this interface for filtering all arguments in the [`\Drupal\views\Plugin\views\display\PathPluginBase::getRoute`](https://api.drupal.org/api/drupal/core%21modules%21views%21src%21Plugin%21views%21display%21PathPluginBase.php/function/PathPluginBase%3A%3AgetRoute/11.x) to skip arguments that implement it.

Another way of fixing this is to exclude the `NULL` argument from the route params by configurations. We can add a new checkbox to the `NULL` argument configuration form that will be responsible for skipping it from route parameters.

In the end, both of these ideas should filter `$argument_ids` to exclude the `NULL` argument (and other arguments that implement either interface or configuration) in the [`\Drupal\views\Plugin\views\display\PathPluginBase::getRoute`](https://api.drupal.org/api/drupal/core%21modules%21views%21src%21Plugin%21views%21display%21PathPluginBase.php/function/PathPluginBase%3A%3AgetRoute/11.x)


